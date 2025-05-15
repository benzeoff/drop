<?php

namespace App\Filament\Resources\TournamentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class MatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'matches';

    protected static ?string $title = 'Матчи';

    public function form(Form $form): Form
    {
        $tournament = $this->ownerRecord;
        $isTeamMode = $tournament->mode === 'team';

        return $form
            ->schema([
                // For 1v1 mode, select players
                Forms\Components\Select::make('player1_id')
                    ->label('Игрок 1')
                    ->relationship('player1', 'name')
                    ->required()
                    ->visible(!$isTeamMode),
                Forms\Components\Select::make('player2_id')
                    ->label('Игрок 2')
                    ->relationship('player2', 'name')
                    ->required()
                    ->visible(!$isTeamMode),
                // For team mode, select teams
                Forms\Components\Select::make('team1_id')
                    ->label('Команда 1')
                    ->relationship('team1', 'name')
                    ->required()
                    ->visible($isTeamMode),
                Forms\Components\Select::make('team2_id')
                    ->label('Команда 2')
                    ->relationship('team2', 'name')
                    ->required()
                    ->visible($isTeamMode),
                // Score fields (apply to both modes)
                Forms\Components\TextInput::make('player1_score')
                    ->label('Счет игрока 1')
                    ->numeric()
                    ->nullable()
                    ->visible(!$isTeamMode),
                Forms\Components\TextInput::make('player2_score')
                    ->label('Счет игрока 2')
                    ->numeric()
                    ->nullable()
                    ->visible(!$isTeamMode),
                Forms\Components\TextInput::make('team1_score')
                    ->label('Счет команды 1')
                    ->numeric()
                    ->nullable()
                    ->visible($isTeamMode),
                Forms\Components\TextInput::make('team2_score')
                    ->label('Счет команды 2')
                    ->numeric()
                    ->nullable()
                    ->visible($isTeamMode),
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидается',
                        'completed' => 'Завершен',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        $tournament = $this->ownerRecord;
        $isTeamMode = $tournament->mode === 'team';

        return $table
            ->columns([
                // For 1v1 mode, show players
                Tables\Columns\TextColumn::make('player1.name')
                    ->label('Игрок 1')
                    ->default('TBD')
                    ->visible(!$isTeamMode),
                Tables\Columns\TextColumn::make('player2.name')
                    ->label('Игрок 2')
                    ->default('TBD')
                    ->visible(!$isTeamMode),
                Tables\Columns\TextColumn::make('player1_score')
                    ->label('Счет 1')
                    ->default('-')
                    ->visible(!$isTeamMode),
                Tables\Columns\TextColumn::make('player2_score')
                    ->label('Счет 2')
                    ->default('-')
                    ->visible(!$isTeamMode),
                // For team mode, show teams
                Tables\Columns\TextColumn::make('team1.name')
                    ->label('Команда 1')
                    ->default('TBD')
                    ->visible($isTeamMode),
                Tables\Columns\TextColumn::make('team2.name')
                    ->label('Команда 2')
                    ->default('TBD')
                    ->visible($isTeamMode),
                Tables\Columns\TextColumn::make('team1_score')
                    ->label('Счет 1')
                    ->default('-')
                    ->visible($isTeamMode),
                Tables\Columns\TextColumn::make('team2_score')
                    ->label('Счет 2')
                    ->default('-')
                    ->visible($isTeamMode),
                // Status applies to both modes
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn(string $state) => $state === 'pending' ? 'Ожидается' : 'Завершен'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать матч'),
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
