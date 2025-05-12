<?php

namespace App\Filament\Resources\TournamentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Notifications\Notification;
use App\Models\GameMatch;

class MatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'matches';

    protected static ?string $title = 'Матчи';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('player1_id')
                    ->label('Игрок 1')
                    ->options($this->ownerRecord->users->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('player2_id')
                    ->label('Игрок 2')
                    ->options($this->ownerRecord->users->pluck('name', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('score')
                    ->label('Счёт')
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'completed' => 'Завершён',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('player1.name')
                    ->label('Игрок 1'),
                Tables\Columns\TextColumn::make('player2.name')
                    ->label('Игрок 2'),
                Tables\Columns\TextColumn::make('score')
                    ->label('Счёт'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn(string $state) => $state === 'pending' ? 'Ожидает' : 'Завершён'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('generateMatches')
                    ->label('Сгенерировать матчи')
                    ->icon('heroicon-o-play')
                    ->action(function () {
                        $tournament = $this->ownerRecord;
                        if ($tournament->matches()->exists()) {
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Матчи уже сгенерированы.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $users = $tournament->users()->pluck('users.id')->shuffle()->toArray();
                        if (count($users) < 2) {
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Недостаточно участников для генерации матчей.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $matches = [];
                        for ($i = 0; $i < count($users); $i += 2) {
                            if (isset($users[$i + 1])) {
                                $matches[] = [
                                    'tournament_id' => $tournament->id,
                                    'player1_id' => $users[$i],
                                    'player2_id' => $users[$i + 1],
                                    'status' => 'pending',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }

                        GameMatch::insert($matches);

                        Notification::make()
                            ->title('Успех')
                            ->body('Матчи успешно сгенерированы!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn() => $this->ownerRecord->users()->count() >= 2 && !$this->ownerRecord->matches()->exists()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
