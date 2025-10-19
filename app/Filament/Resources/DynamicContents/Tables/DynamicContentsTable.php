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
            ->modifyQueryUsing(fn ($query) => $query->with(['multimedia', 'gift', 'profile', 'event', 'product', 'tourist', 'business', 'socialLinks', 'skills']))
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
                        $summary = [];

                        // Mostrar información relevante según el tipo usando tablas normalizadas
                        switch ($record->type) {
                            case 'GIFT':
                                if ($record->gift) {
                                    if ($record->gift->sender_name) {
                                        $summary[] = "De: {$record->gift->sender_name}";
                                    }
                                    if ($record->gift->recipient_name) {
                                        $summary[] = "Para: {$record->gift->recipient_name}";
                                    }
                                }
                                if ($record->multimedia) {
                                    if ($record->multimedia->gallery_images) {
                                        $summary[] = count($record->multimedia->gallery_images) . " fotos";
                                    }
                                    if ($record->multimedia->video_url) {
                                        $summary[] = "Video";
                                    }
                                    if ($record->multimedia->audio_url) {
                                        $summary[] = "Audio: " . $record->multimedia->audio_type;
                                    }
                                }

                                break;

                            case 'MENU': // DEPRECATED - tipo migrado a BUSINESS
                            case 'BUSINESS':
                                if ($record->business) {
                                    if ($record->business->contact_phone) {
                                        $summary[] = "Tel: {$record->business->contact_phone}";
                                    }
                                    if ($record->business->directProducts) {
                                        $summary[] = $record->business->directProducts->count() . " productos";
                                    }
                                }

                                break;

                            case 'PROFILE':
                                if ($record->profile) {
                                    if ($record->profile->contact_email) {
                                        $summary[] = "Email: {$record->profile->contact_email}";
                                    }
                                }
                                if ($record->socialLinks) {
                                    $summary[] = $record->socialLinks->count() . " redes";
                                }

                                break;

                            case 'EVENT':
                                if ($record->event) {
                                    if ($record->event->event_location) {
                                        $summary[] = "Lugar: {$record->event->event_location}";
                                    }
                                    if ($record->event->event_start_date) {
                                        $summary[] = "Fecha: {$record->event->event_start_date->format('d/m/Y')}";
                                    }
                                    if ($record->event->event_organizer) {
                                        $summary[] = "Org: {$record->event->event_organizer}";
                                    }
                                }

                                break;

                            case 'PRODUCT':
                                if ($record->product) {
                                    if ($record->product->product_price) {
                                        $summary[] = "Precio: {$record->product->getFormattedPrice()}";
                                    }
                                    if ($record->product->product_stock) {
                                        $summary[] = "Stock: {$record->product->product_stock}";
                                    }
                                    if ($record->product->product_sku) {
                                        $summary[] = "SKU: {$record->product->product_sku}";
                                    }
                                }

                                break;

                            case 'TOURIST':
                                if ($record->tourist) {
                                    if ($record->tourist->location_name) {
                                        $summary[] = "Lugar: {$record->tourist->location_name}";
                                    }
                                    if ($record->tourist->contact_phone) {
                                        $summary[] = "Tel: {$record->tourist->contact_phone}";
                                    }
                                    if ($record->tourist->website_url) {
                                        $summary[] = "Web";
                                    }
                                }

                                break;
                        }

                        // Fallback a JSON para retrocompatibilidad
                        if (empty($summary) && is_array($state)) {
                            if (isset($state['from'])) {
                                $summary[] = "De: {$state['from']}";
                            }
                            if (isset($state['to'])) {
                                $summary[] = "Para: {$state['to']}";
                            }
                            if (isset($state['multimedia']['gallery'])) {
                                $summary[] = count($state['multimedia']['gallery']) . " fotos";
                            }
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
                TextColumn::make('gift.sender_name')
                    ->label('De (Gift)')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('gift.recipient_name')
                    ->label('Para (Gift)')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('menu.restaurant_name')
                    ->label('Restaurante')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('profile.contact_email')
                    ->label('Email (Profile)')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('multimedia.video_url')
                    ->label('Video')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(30),
                TextColumn::make('multimedia.audio_url')
                    ->label('Audio')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(30),
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
