<?php

namespace App\Filament\Resources\TournamentResource\Pages;

use App\Filament\Resources\TournamentResource;
use App\Models\GameMatch;
use App\Models\Tournament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class CreateMatches extends Page
{
    protected static string $resource = TournamentResource::class;

    protected static string $view = 'filament.resources.tournament-resource.pages.create-matches';

    public $tournament;

    public $matches = [];

    public function mount($record)
    {
        $this->tournament = Tournament::findOrFail($record);
        if ($this->tournament->mode !== 'team') {
            abort(403, 'Этот функционал доступен только для командных турниров.');
        }
        if ($this->tournament->matches()->exists()) {
            abort(403, 'Матчи уже сгенерированы.');
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Repeater::make('matches')
                ->label('Матчи')
                ->schema([
                    Select::make('team1_id')
                        ->label('Команда 1')
                        ->options($this->tournament->teams->pluck('name', 'id'))
                        ->required(),
                    Select::make('team2_id')
                        ->label('Команда 2')
                        ->options($this->tournament->teams->pluck('name', 'id'))
                        ->required()
                        ->different('team1_id'),
                ])
                ->columns(2),
        ];
    }

    public function submit()
    {
        $matches = $this->matches;
        $createdMatches = [];

        foreach ($matches as $match) {
            $createdMatches[] = [
                'tournament_id' => $this->tournament->id,
                'team1_id' => $match['team1_id'],
                'team2_id' => $match['team2_id'],
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        GameMatch::insert($createdMatches);

        Notification::make()
            ->title('Успех')
            ->body('Матчи успешно созданы!')
            ->success()
            ->send();

        return redirect()->route('filament.resources.tournaments.index');
    }
}
