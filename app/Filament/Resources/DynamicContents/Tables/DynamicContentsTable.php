<?php

namespace App\Filament\Resources\DynamicContents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DynamicContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content_id')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('gift_subtype')
                    ->searchable(),
                TextColumn::make('tier')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('data')
                    ->label('Resumen de Datos')
                    ->formatStateUsing(function ($state, $record) {
                        if (!is_array($state)) return $state;
                        
                        $summary = [];
                        
                        // Mostrar información relevante según el tipo
                        switch ($record->type) {
                            case 'GIFT':
                                if (isset($state['from'])) $summary[] = "De: {$state['from']}";
                                if (isset($state['to'])) $summary[] = "Para: {$state['to']}";
                                if (isset($state['multimedia']['gallery'])) $summary[] = count($state['multimedia']['gallery']) . " fotos";
                                if (isset($state['multimedia']['video'])) $summary[] = "Video";
                                if (isset($state['multimedia']['audio'])) $summary[] = "Audio: " . $state['multimedia']['audio']['type'];
                                break;
                                
                            case 'MENU':
                                if (isset($state['restaurant_info']['phone'])) $summary[] = "Tel: {$state['restaurant_info']['phone']}";
                                if (isset($state['menu_items'])) $summary[] = count($state['menu_items']) . " platos";
                                break;
                                
                            case 'PROFILE':
                                if (isset($state['contact_info']['email'])) $summary[] = "Email: {$state['contact_info']['email']}";
                                if (isset($state['social_links'])) $summary[] = count($state['social_links']) . " redes";
                                break;
                        }
                        
                        return empty($summary) ? 'Datos disponibles' : implode(" | ", $summary);
                    })
                    ->tooltip(fn ($record) => is_array($record->data) ? json_encode($record->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $record->data)
                    ->wrap(),
                TextColumn::make('data')
                    ->label('JSON Completo')
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->limit(50)
                    ->tooltip(fn ($record) => is_array($record->data) ? json_encode($record->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $record->data)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image_url'),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_draft_update')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('post_publish_modifications')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('nfcToken.name')
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
