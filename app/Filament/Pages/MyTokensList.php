<?php

namespace App\Filament\Pages;

use App\Models\NfcToken;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

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
                    ->colors([
                        'success' => 'GIFT',
                        'info' => 'MENU',
                        'warning' => 'PROFILE',
                        'primary' => 'EVENT',
                        'secondary' => 'PRODUCT',
                        'danger' => 'TOURIST',
                    ]),

                BadgeColumn::make('is_active')
                    ->label('Estado')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ]),

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
                    ->visible(fn (NfcToken $record) => 
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
                    ->visible(fn (NfcToken $record) => 
                        $record->content_type === 'PROFILE' && 
                        auth()->user()->can('configure_own_tokens') &&
                        $record->user_id === auth()->id()
                    )
                    ->url(fn (NfcToken $record): string => "/admin/my-tokens/{$record->id}/configure-profile")
                    ->openUrlInNewTab(false),

                Action::make('preview')
                    ->label('Vista Previa')
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->url(fn (NfcToken $record): string => "/token/{$record->token_id}")
                    ->openUrlInNewTab(true),

                Action::make('view')
                    ->label('Info')
                    ->icon('heroicon-o-information-circle')
                    ->color('gray')
                    ->action(function (NfcToken $record) {
                        Notification::make()
                            ->title('Token: ' . $record->name)
                            ->body('ID: ' . $record->token_id . ' | Tipo: ' . $record->content_type)
                            ->info()
                            ->send();
                    }),

                Action::make('copy_url')
                    ->label('Copiar URL')
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->action(function (NfcToken $record) {
                        $url = config('app.url') . '/token/' . $record->token_id;
                        
                        Notification::make()
                            ->title('URL copiada')
                            ->body($url)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Copiar URL del Token')
                    ->modalDescription(fn (NfcToken $record) => 'URL: ' . config('app.url') . '/token/' . $record->token_id)
                    ->modalSubmitActionLabel('Copiar'),
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