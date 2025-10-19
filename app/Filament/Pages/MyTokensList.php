<?php

namespace App\Filament\Pages;

use App\Models\NfcToken;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class MyTokensList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Mis Tokens';

    protected static ?string $navigationLabel = 'Mis Tokens';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'my-tokens';

    public function getView(): string
    {
        return 'filament.pages.my-tokens-list';
    }

    public function getTitle(): string
    {
        $tokensCount = $this->getTableQuery()->count();

        return "Mis Tokens ({$tokensCount})";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                TextColumn::make('token_id')
                    ->label('Token ID')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Token ID copiado')
                    ->limit(20),

                BadgeColumn::make('content_type')
                    ->label('Tipo')
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
                    ->colors([
                        'success' => 'GIFT',
                        'info' => 'BUSINESS',
                        'warning' => 'PROFILE',
                        'primary' => 'TOURIST',
                        'secondary' => 'BUS_STOP',
                    ]),

                BadgeColumn::make('is_active')
                    ->label('Estado')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ]),

                TextColumn::make('preview_backend')
                    ->label('Vista Previa Backend')
                    ->state(function ($record) {
                        return $record->is_active ? 'active' : 'inactive';
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->is_active) {
                            $url = url("/token/{$record->token_id}");

                            return new HtmlString("<a href='{$url}' target='_blank' class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors duration-200'>
                                <svg class='w-3 h-3 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14'></path>
                                </svg>
                                Backend
                            </a>");
                        }

                        return new HtmlString("<span class='text-gray-400 text-xs'>Inactivo</span>");
                    })
                    ->html(),

                TextColumn::make('preview_frontend')
                    ->label('Vista Previa Front')
                    ->state(function ($record) {
                        return $record->is_active ? 'active' : 'inactive';
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->is_active) {
                            $url = "http://127.0.0.1:3000/token/{$record->token_id}";

                            return new HtmlString("<a href='{$url}' target='_blank' class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 transition-colors duration-200'>
                                <svg class='w-3 h-3 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14'></path>
                                </svg>
                                Frontend
                            </a>");
                        }

                        return new HtmlString("<span class='text-gray-400 text-xs'>Inactivo</span>");
                    })
                    ->html(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                // Agregar filtros si necesario
            ])
            ->actions([
                Action::make('configure')
                    ->label('Configurar')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('primary')
                    ->visible(
                        fn (NfcToken $record) =>
                        $record->content_type === 'GIFT' &&
                        auth()->user()->can('configure_own_tokens') &&
                        $record->user_id === auth()->id()
                    )
                    ->url(fn (NfcToken $record): string => "/admin/my-tokens/{$record->id}/configure")
                    ->openUrlInNewTab(false),

                Action::make('configure_profile')
                    ->label('Configurar Perfil')
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->visible(
                        fn (NfcToken $record) =>
                        $record->content_type === 'PROFILE' &&
                        auth()->user()->can('configure_own_tokens') &&
                        $record->user_id === auth()->id()
                    )
                    ->url(fn (NfcToken $record): string => "/admin/my-tokens/{$record->id}/configure-profile")
                    ->openUrlInNewTab(false),
            ])
            ->bulkActions([
                // Agregar acciones bulk si necesario
            ])
            ->defaultSort('updated_at', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s')
            ->emptyStateHeading('No tienes tokens')
            ->emptyStateDescription('Aún no tienes ningún token NFC asignado.')
            ->emptyStateIcon('heroicon-o-qr-code');
    }

    protected function getTableQuery(): Builder
    {
        return NfcToken::query()
            ->where('user_id', auth()->id())
            ->with(['user']);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('view_own_tokens');
    }

    public function getHeading(): string
    {
        return 'Gestiona todos tus tokens NFC';
    }

    public function getSubheading(): ?string
    {
        return 'Aquí puedes ver todos tus tokens, configurarlos y gestionar su contenido.';
    }
}
