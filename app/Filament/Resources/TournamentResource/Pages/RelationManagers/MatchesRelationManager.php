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
        return $form
            ->schema([
                Forms\Components\Select::make('player1_id')
                    ->label('Игрок 1')
                    ->relationship('player1', 'name')
                    ->required(),
                Forms\Components\Select::make('player2_id')
                    ->label('Игрок 2')
                    ->relationship('player2', 'name')
                    ->required(),
                Forms\Components\TextInput::make('player1_score')
                    ->label('Счет игрока 1')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('player2_score')
                    ->label('Счет игрока 2')
                    ->numeric()
                    ->nullable(),
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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('player1.name')
                    ->label('Игрок 1')
                    ->default('TBD'),
                Tables\Columns\TextColumn::make('player2.name')
                    ->label('Игрок 2')
                    ->default('TBD'),
                Tables\Columns\TextColumn::make('player1_score')
                    ->label('Счет 1')
                    ->default('-'),
                Tables\Columns\TextColumn::make('player2_score')
                    ->label('Счет 2')
                    ->default('-'),
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
