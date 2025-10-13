<?php

namespace App\Filament\Resources\ContentBusinessGroups\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MemberBusinessesRelationManager extends RelationManager
{
    protected static string $relationship = 'memberBusinesses';
    
    protected static ?string $recordTitleAttribute = 'business_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('business_name')
            ->columns([
                TextColumn::make('business_name')
                    ->searchable(),
                TextColumn::make('business_type'),
                TextColumn::make('contact_phone'),
                TextColumn::make('address')
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}