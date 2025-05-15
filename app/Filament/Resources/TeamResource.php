<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TournamentResource\Pages;
use App\Filament\Resources\TournamentResource\RelationManagers;
use App\Models\Tournament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class TournamentResource extends Resource
{
    protected static ?string $model = Tournament::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Турниры';

    protected static ?string $pluralLabel = 'Турниры';

    protected static ?string $navigationGroup = 'Турниры';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название турнира')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('game')
                    ->label('Игра')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('date')
                    ->label('Дата и время')
                    ->required()
                    ->minDate(now()),
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->nullable()
                    ->rows(4),
                Forms\Components\TextInput::make('prize')
                    ->label('Призы')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('max_participants')
                    ->label('Максимум участников')
                    ->required()
                    ->numeric()
                    ->minValue(2),
                Forms\Components\Select::make('mode')
                    ->label('Режим')
                    ->options([
                        '1v1' => '1 на 1',
                        'team' => 'Команда против команды',
                    ])
                    ->default('1v1')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('game')
                    ->label('Игра')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Участники')
                    ->counts('users')
                    ->visible(fn($record) => $record ? $record->mode === '1v1' : false),
                Tables\Columns\TextColumn::make('teams_count')
                    ->label('Команды')
                    ->counts('teams')
                    ->visible(fn($record) => $record ? $record->mode === 'team' : false),
                Tables\Columns\TextColumn::make('max_participants')
                    ->label('Макс. участников'),
                Tables\Columns\TextColumn::make('participants')
                    ->label('Состав участников')
                    ->getStateUsing(function ($record) {
                        if (!$record) {
                            return '-';
                        }
                        if ($record->mode === '1v1') {
                            return $record->users->pluck('name')->join(', ');
                        }
                        return $record->teams->map(fn($team) => $team->name . ' (' . $team->users->pluck('name')->join(', ') . ')')->join('; ');
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generate_matches')
                    ->label('Сгенерировать матчи')
                    ->icon('heroicon-o-play')
                    ->action(function (Tournament $tournament) {
                        if ($tournament->matches()->exists()) {
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Матчи уже сгенерированы.')
                                ->danger()
                                ->send();
                            return;
                        }

                        if ($tournament->mode === '1v1') {
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
                            \App\Models\GameMatch::insert($matches);
                        } else {
                            // Для команд перенаправляем на форму выбора пар
                            return redirect()->route('filament.resources.tournaments.create-matches', $tournament->id);
                        }

                        Notification::make()
                            ->title('Успех')
                            ->body('Матчи успешно сгенерированы!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn(Tournament $tournament) => (
                        ($tournament->mode === '1v1' && $tournament->users()->count() >= 2) ||
                        ($tournament->mode === 'team' && $tournament->teams()->count() >= 2)
                    ) && !$tournament->matches()->exists()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
            RelationManagers\MatchesRelationManager::class,
            RelationManagers\ResourcesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTournaments::route('/'),
            'create' => Pages\CreateTournament::route('/create'),
            'edit' => Pages\EditTournament::route('/{record}/edit'),
            'create-matches' => Pages\CreateMatches::route('/{record}/create-matches'),
        ];
    }
}
