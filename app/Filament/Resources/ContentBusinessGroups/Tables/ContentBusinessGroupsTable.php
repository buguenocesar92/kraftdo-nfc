<?php

namespace App\Filament\Resources\ContentBusinessGroups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class ContentBusinessGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->circular()
                    ->size(40),
                
                TextColumn::make('group_name')
                    ->label('Nombre del Grupo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('group_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'food_court' => 'Food Court',
                        'mall' => 'Centro Comercial',
                        'market' => 'Mercado',
                        'fair' => 'Feria',
                        'plaza' => 'Plaza de Comidas',
                        default => ucfirst($state),
                    }),
                
                TextColumn::make('memberBusinesses_count')
                    ->label('Miembros')
                    ->counts('memberBusinesses'),
                
                TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('contact_phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(),
                
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group_type')
                    ->label('Tipo de Grupo')
                    ->options([
                        'food_court' => 'Food Court',
                        'mall' => 'Centro Comercial',
                        'market' => 'Mercado',
                        'fair' => 'Feria',
                        'plaza' => 'Plaza de Comidas',
                    ]),
                
                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo Activos')
                    ->falseLabel('Solo Inactivos'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
