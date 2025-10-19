<?php

namespace App\Filament\Resources\NfcTokens\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class NfcTokensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('token_id')
                    ->label('Token UUID')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('content_type')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'GIFT' => '🎁 Regalo',
                            'BUSINESS' => '🏢 Negocio',
                            'PROFILE' => '👤 Perfil',
                            'TOURIST' => '🗺️ Turismo',
                            'BUS_STOP' => '🚌 Paradero',
                            default => $state
                        };
                    })
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'GIFT' => 'success',
                            'BUSINESS' => 'info',
                            'PROFILE' => 'warning',
                            'TOURIST' => 'primary',
                            'BUS_STOP' => 'secondary',
                            default => 'gray'
                        };
                    })
                    ->searchable(),
                TextColumn::make('customization_plan')
                    ->searchable(),
                TextColumn::make('purchase_price')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('purchased_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('purchase_currency')
                    ->searchable(),
                TextColumn::make('cost_per_view')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_investment_views')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('preview_backend')
                    ->label('Vista Previa Backend')
                    ->state(function ($record) {
                        return $record->is_active ? 'active' : 'inactive';
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->is_active) {
                            $url = url("/token/{$record->token_id}");

                            return new HtmlString("<a href='{$url}' target='_blank' class='inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors duration-200'>
                                <svg class='w-3 h-3 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14'></path>
                                </svg>
                                Ver Backend
                            </a>");
                        }

                        return new HtmlString("<span class='text-gray-400 text-xs'>Inactivo</span>");
                    })
                    ->html()
                    ->toggleable(),
                TextColumn::make('preview_frontend')
                    ->label('Vista Previa Frontend')
                    ->state(function ($record) {
                        return $record->is_active ? 'active' : 'inactive';
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->is_active) {
                            $url = "http://127.0.0.1:3000/token/{$record->token_id}";

                            return new HtmlString("<a href='{$url}' target='_blank' class='inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 transition-colors duration-200'>
                                <svg class='w-3 h-3 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14'></path>
                                </svg>
                                Vista Previa Front
                            </a>");
                        }

                        return new HtmlString("<span class='text-gray-400 text-xs'>Inactivo</span>");
                    })
                    ->html()
                    ->toggleable(),
                TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable(),
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
