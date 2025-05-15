<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Models\Tournament;
use App\Models\Team;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationLabel = 'Заявки';

    protected static ?string $pluralLabel = 'Заявки';

    protected static ?string $navigationGroup = 'Турниры';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_id')
                    ->label('Команда')
                    ->options(Team::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('tournament_id')
                    ->label('Турнир')
                    ->options(Tournament::where('mode', 'team')->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Ожидает',
                        'approved' => 'Одобрено',
                        'rejected' => 'Отклонено',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team_name')
                    ->label('Команда'),
                Tables\Columns\TextColumn::make('tournament_name')
                    ->label('Турнир'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Ожидает',
                        'approved' => 'Одобрено',
                        'rejected' => 'Отклонено',
                        default => 'Неизвестный статус',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Одобрить')
                    ->action(function (Application $application) {
                        if ($application->status !== 'pending') {
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Заявка уже обработана.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $tournament = $application->tournament;
                        if ($tournament->isFullForTeams()) {
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Турнир заполнен.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $application->update(['status' => 'approved']);
                        $tournament->teams()->attach($application->team_id);

                        // Уведомление участникам
                        $team = $application->team;
                        foreach ($team->users as $user) {
                            $user->notifications()->create([
                                'title' => 'Заявка одобрена',
                                'message' => "Ваша команда {$team->name} была добавлена в турнир {$tournament->name}.",
                            ]);
                        }

                        Notification::make()
                            ->title('Успех')
                            ->body('Заявка одобрена!')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(Application $application) => $application->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->label('Отклонить')
                    ->action(function (Application $application) {
                        if ($application->status !== 'pending') {
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Заявка уже обработана.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $application->update(['status' => 'rejected']);
                        $tournament = $application->tournament;
                        $team = $application->team;

                        // Уведомление участникам
                        foreach ($team->users as $user) {
                            $user->notifications()->create([
                                'title' => 'Заявка отклонена',
                                'message' => "Ваша команда {$team->name} не была добавлена в турнир {$tournament->name}.",
                            ]);
                        }

                        Notification::make()
                            ->title('Успех')
                            ->body('Заявка отклонена.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(Application $application) => $application->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
