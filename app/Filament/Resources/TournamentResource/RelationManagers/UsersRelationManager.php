<?php

namespace App\Filament\Resources\TournamentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Notifications\Notification;
use App\Models\Notification as UserNotification;
use Illuminate\Support\Facades\Log;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $title = 'Участники';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Имя')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Добавить участника')
                    ->preloadRecordSelect()
                    ->action(function ($data, $livewire) {
                        Log::info('Attach action started', [
                            'record_id' => $data['recordId'],
                            'tournament_id' => $livewire->ownerRecord->id,
                        ]);

                        $user = \App\Models\User::find($data['recordId']);
                        $tournament = $livewire->ownerRecord;

                        if (!$user || !$tournament) {
                            Log::error('Invalid user or tournament', [
                                'user_id' => $data['recordId'],
                                'tournament_id' => $tournament ? $tournament->id : null,
                            ]);
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Пользователь или турнир не найдены.')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            $tournament->users()->syncWithoutDetaching($user->id);
                            Log::info('User attached to tournament', [
                                'user_id' => $user->id,
                                'tournament_id' => $tournament->id,
                            ]);

                            $notification = UserNotification::create([
                                'user_id' => $user->id,
                                'title' => 'Регистрация на турнир',
                                'message' => "Вы зарегистрированы на турнир {$tournament->name}.",
                            ]);
                            Log::info('Notification created', [
                                'notification_id' => $notification->id,
                                'user_id' => $user->id,
                            ]);

                            Notification::make()
                                ->title('Успех')
                                ->body('Участник добавлен и уведомление создано.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Error in attach action', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Ошибка: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make()->label('Удалить выбранных'),
            ]);
    }
}
