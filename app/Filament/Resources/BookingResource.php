<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Бронирования';

    protected static ?string $modelLabel = 'Бронирования';

    protected static ?string $pluralModelLabel = 'Бронирования';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Имя')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('resource_id')
                    ->label('Ресурс')
                    ->relationship('resource', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Начало')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('Конец')
                    ->required()
                    ->after('start_time')
                    ->rules(['after:start_time']),
                Forms\Components\TextInput::make('price')
                    ->label('Стоимость')
                    ->numeric()
                    ->required()
                    ->prefix('₽'),
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                        null => 'Pending',
                    ])
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Имя')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resource.name')
                    ->label('Ресурс')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Начало')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Конец')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Стоимость')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'cancelled' => 'danger',
                        'completed' => 'success',
                        null => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                        null => 'Pending',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
