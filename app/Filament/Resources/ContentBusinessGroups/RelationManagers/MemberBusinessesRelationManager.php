<?php

namespace App\Filament\Resources\ContentBusinessGroups\RelationManagers;

use App\Filament\Resources\ContentBusinesses\ContentBusinessResource;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
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
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function ($query) {
                        // Obtener IDs de negocios ya asignados a cualquier grupo
                        $assignedBusinessIds = \DB::table('business_group_members')
                            ->pluck('member_business_id')
                            ->unique();

                        // Filtrar solo negocios no asignados - especificar tabla para evitar ambigüedad
                        return $query->whereNotIn('content_businesses.id', $assignedBusinessIds);
                    })
                    ->recordSelectSearchColumns(['business_name', 'contact_phone'])
                    ->modalHeading('Attach Business')
                    ->modalDescription('Select businesses that are not already part of any business group')
                    ->successNotificationTitle('Business attached successfully'),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn ($record) => ContentBusinessResource::getUrl('edit', ['record' => $record])),
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
