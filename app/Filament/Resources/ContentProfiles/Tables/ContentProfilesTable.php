<?php

namespace App\Filament\Resources\ContentProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContentProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dynamicContent.title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nombre completo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('contact_email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('contact_phone')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('contact_website')
                    ->label('Sitio Web')
                    ->searchable(),
                TextColumn::make('socialLinks_count')
                    ->label('Redes Sociales')
                    ->counts('socialLinks')
                    ->badge()
                    ->color('success'),
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
