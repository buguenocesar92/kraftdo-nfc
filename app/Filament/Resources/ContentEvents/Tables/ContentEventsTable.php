<?php

namespace App\Filament\Resources\ContentEvents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContentEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dynamicContent.title')
                    ->searchable(),
                TextColumn::make('event_location')
                    ->searchable(),
                TextColumn::make('event_start_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('event_end_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('event_organizer')
                    ->searchable(),
                TextColumn::make('ticket_price')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ticket_currency')
                    ->searchable(),
                TextColumn::make('registration_url')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
