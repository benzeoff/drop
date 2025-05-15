<?php

namespace App\Filament\Resources\TournamentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Notifications\Notification;
use App\Models\Application;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    protected static ?string $title = 'Заявки на участие';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_id')
                    ->label('Команда')
                    ->relationship('team', 'name')
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'approved' => 'Одобрено',
                        'rejected' => 'Отклонено',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Команда')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'pending' => 'Ожидает',
                        'approved' => 'Одобрено',
                        'rejected' => 'Отклонено',
                        default => 'Неизвестно',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата подачи')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'approved' => 'Одобрено',
                        'rejected' => 'Отклонено',
                    ]),
            ])
            ->headerActions([
                // No create action since applications are submitted by users
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (Application $record) {
                        // If the application is approved, attach the team to the tournament
                        if ($record->status === 'approved') {
                            $tournament = $record->tournament;
                            $team = $record->team;

                            // Check if the team is already attached to avoid duplicates
                            if (!$tournament->teams()->where('teams.id', $team->id)->exists()) {
                                if ($tournament->isFullForTeams()) {
                                    Notification::make()
                                        ->title('Ошибка')
                                        ->body('Турнир уже заполнен командами.')
                                        ->danger()
                                        ->send();
                                    // Optionally revert the status to pending
                                    $record->update(['status' => 'pending']);
                                    return;
                                }

                                $tournament->teams()->attach($team->id);
                                Notification::make()
                                    ->title('Успех')
                                    ->body("Команда {$team->name} добавлена в турнир.")
                                    ->success()
                                    ->send();
                            }
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
