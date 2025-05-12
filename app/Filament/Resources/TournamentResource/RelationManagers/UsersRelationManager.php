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
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Добавить участника')
                    ->preloadRecordSelect()
                    ->after(function ($data, $livewire) {
                        $user = \App\Models\User::find($data['recordId']);
                        $tournament = $livewire->ownerRecord;

                        Log::info('Attempting to attach user to tournament', [
                            'user_id' => $data['recordId'],
                            'tournament_id' => $tournament->id,
                            'current_participants' => $tournament->users()->count(),
                            'max_participants' => $tournament->max_participants,
                        ]);

                        // Проверка на максимальное количество участников
                        if ($tournament->users()->count() >= $tournament->max_participants) {
                            Log::info('Max participants reached for tournament', ['tournament_id' => $tournament->id]);
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Достигнуто максимальное количество участников.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Проверка валидности пользователя и турнира
                        if (!$user || !$tournament) {
                            Log::error('Invalid user or tournament', ['user' => $user, 'tournament' => $tournament]);
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Недопустимый пользователь или турнир.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Привязка пользователя к турниру
                        $tournament->users()->attach($user->id);
                        Log::info('User attached to tournament', ['user_id' => $user->id, 'tournament_id' => $tournament->id]);

                        // Создание уведомления с обработкой ошибок
                        try {
                            Notification::create([
                                'user_id' => $user->id,
                                'title' => 'Регистрация на турнир: ' . $tournament->name,
                                'message' => "Вы успешно зарегистрированы на турнир: {$tournament->name}. Игра: {$tournament->game}. Дата: {$tournament->date->format('d.m.Y H:i')}.",
                            ]);
                            Log::info('Notification created successfully', ['user_id' => $user->id]);
                        } catch (\Exception $e) {
                            Log::error('Failed to create notification', ['error' => $e->getMessage()]);
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Не удалось создать уведомление: ' . $e->getMessage())
                                ->danger()
                                ->send();
                            return;
                        }

                        Notification::make()
                            ->title('Успех')
                            ->body('Участник добавлен, уведомление отправлено.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make()
                    ->label('Удалить выбранных'),
            ]);
    }
}
