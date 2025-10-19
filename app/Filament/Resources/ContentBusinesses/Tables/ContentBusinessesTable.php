<?php

namespace App\Filament\Resources\ContentBusinesses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContentBusinessesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->circular()
                    ->size(50),

                TextColumn::make('business_name')
                    ->label('Nombre del Negocio')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('business_type')
                    ->label('Tipo')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'restaurant' => '🍽️ Restaurante',
                            'retail' => '🛍️ Retail',
                            'service' => '🔧 Servicios',
                            'fair' => '🎪 Feria',
                            'other' => '📋 Otro',
                            default => $state ?? 'Sin tipo'
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'restaurant' => 'warning',
                            'retail' => 'success',
                            'service' => 'info',
                            'fair' => 'primary',
                            'other' => 'gray',
                            default => 'gray'
                        };
                    }),

                TextColumn::make('contact_phone')
                    ->label('Teléfono')
                    ->searchable(),

                TextColumn::make('contact_email')
                    ->label('Email')
                    ->searchable(),

                IconColumn::make('catalog_enabled')
                    ->label('Catálogo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('products_count')
                    ->label('Productos')
                    ->counts('products')
                    ->badge()
                    ->color('success')
                    ->visible(fn ($record) => $record?->catalog_enabled ?? false),

                TextColumn::make('socialLinks_count')
                    ->label('Redes Sociales')
                    ->counts('socialLinks')
                    ->badge()
                    ->color('info'),

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
