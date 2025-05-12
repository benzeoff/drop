<?php

namespace App\Filament\Resources\TournamentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Notifications\Notification;

class ResourcesRelationManager extends RelationManager
{
    protected static string $relationship = 'resources';

    protected static ?string $title = 'Ресурсы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название ресурса')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn(string $state) => $state === 'computer' ? 'Компьютер' : 'Зона'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Категория'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn(string $state) => $state === 'available' ? 'Доступен' : 'Забронирован'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'computer' => 'Компьютер',
                        'zone' => 'Зона',
                    ])
                    ->label('Тип ресурса'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Добавить ресурс')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn($query) => $query->where('status', 'available'))
                    ->action(function ($data, $livewire) {
                        $resource = \App\Models\Resource::find($data['recordId']);
                        $tournament = $livewire->ownerRecord;

                        // Проверка, не забронирован ли ресурс на время турнира
                        $conflictingBooking = \App\Models\Booking::where('resource_id', $resource->id)
                            ->where(function ($query) use ($tournament) {
                                $query->whereBetween('start_time', [$tournament->date->subHour(), $tournament->date->addHours(6)])
                                    ->orWhereBetween('end_time', [$tournament->date->subHour(), $tournament->date->addHours(6)]);
                            })
                            ->exists();

                        if ($conflictingBooking) {
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Ресурс уже забронирован на время турнира.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $livewire->ownerRecord->resources()->attach($resource->id);
                        $resource->update(['status' => 'booked']);

                        Notification::make()
                            ->title('Успех')
                            ->body('Ресурс успешно добавлен к турниру!')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Удалить')
                    ->action(function ($record, $livewire) {
                        $livewire->ownerRecord->resources()->detach($record->id);
                        $record->update(['status' => 'available']);
                        Notification::make()
                            ->title('Успех')
                            ->body('Ресурс успешно удалён из турнира.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make()
                    ->label('Удалить выбранные')
                    ->action(function ($records, $livewire) {
                        foreach ($records as $record) {
                            $livewire->ownerRecord->resources()->detach($record->id);
                            $record->update(['status' => 'available']);
                        }
                        Notification::make()
                            ->title('Успех')
                            ->body('Ресурсы успешно удалены из турнира.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
