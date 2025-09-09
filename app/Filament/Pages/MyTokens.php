<?php

namespace App\Filament\Pages;

use App\Models\ContentGift;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class MyTokens extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $title = 'Configurar Token';

    protected static ?string $navigationLabel = 'Mis Tokens';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'my-tokens/{tokenId}';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];
    public ?NfcToken $token = null;
    public ?ContentGift $contentGift = null;
    public ?int $selectedTokenId = null;

    public function getView(): string
    {
        return 'filament.pages.my-tokens';
    }

    public function getTitle(): string
    {
        if ($this->token) {
            return "Configurar Token #{$this->token->id}";
        }
        return 'Configurar Token';
    }

    public function mount($tokenId): void
    {
        // El tokenId es requerido ahora
        $this->selectedTokenId = $tokenId;
        $this->loadToken($this->selectedTokenId);
        
        // Si no se pudo cargar el token, mostrar error
        if (!$this->token) {
            abort(404, 'Token no encontrado o no tienes permisos para acceder a él.');
        }
    }

    public function loadToken($tokenId): void
    {
        try {
            $this->token = NfcToken::findOrFail($tokenId);
            
            // Verificar que el token pertenece al usuario actual
            if ($this->token->user_id !== auth()->id()) {
                Notification::make()
                    ->title('Acceso denegado')
                    ->body('No tienes permisos para acceder a este token.')
                    ->danger()
                    ->send();
                $this->token = null;
                $this->selectedTokenId = null;
                return;
            }

            // Verificar que es un token de regalo
            if ($this->token->content_type !== 'GIFT') {
                Notification::make()
                    ->title('Tipo de token incorrecto')
                    ->body('Este token no es de tipo regalo.')
                    ->warning()
                    ->send();
                $this->token = null;
                $this->selectedTokenId = null;
                return;
            }

            // Obtener o crear el ContentGift asociado
            $this->contentGift = $this->getOrCreateContentGift();
            
            if ($this->contentGift) {
                // Llenar el formulario con los datos existentes
                $data = array_merge($this->contentGift->toArray(), [
                    'selectedTokenId' => $this->selectedTokenId,
                    'token_name' => $this->token->name,
                    'token_id' => $this->token->token_id,
                ]);
                $this->form->fill($data);
                
                Notification::make()
                    ->title('Token cargado')
                    ->body('Se ha cargado correctamente el token y su información.')
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al cargar token')
                ->body('No se pudo encontrar el token especificado.')
                ->danger()
                ->send();
            $this->token = null;
            $this->selectedTokenId = null;
        }
    }

    protected function getOrCreateContentGift(): ?ContentGift
    {
        if (!$this->token) {
            return null;
        }

        // Obtener el DynamicContent del token
        $dynamicContent = $this->token->dynamicContent;
        
        if (!$dynamicContent) {
            Notification::make()
                ->title('Contenido no encontrado')
                ->body('El token no tiene contenido dinámico asociado.')
                ->warning()
                ->send();
            return null;
        }

        // Buscar o crear el ContentGift
        $contentGift = ContentGift::where('dynamic_content_id', $dynamicContent->id)->first();
        
        if (!$contentGift) {
            $contentGift = ContentGift::create([
                'dynamic_content_id' => $dynamicContent->id,
                'sender_name' => auth()->user()->name,
                'recipient_name' => '',
                'message' => '',
            ]);
        }

        return $contentGift;
    }


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Configuración del Regalo')
                    ->description('Configura los datos del regalo asociado a este token')
                    ->schema([
                        TextInput::make('sender_name')
                            ->label('Nombre del remitente')
                            ->required(),
                        TextInput::make('recipient_name')
                            ->label('Nombre del destinatario')
                            ->required(),
                        Textarea::make('message')
                            ->label('Mensaje personalizado')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data')
            ->model(ContentGift::class);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('update')
                ->label('Actualizar Regalo')
                ->submit('update')
                ->color('success'),
            Action::make('reset')
                ->label('Restaurar Valores')
                ->action('resetForm')
                ->color('gray'),
        ];
    }

    public function update(): void
    {
        if (!$this->contentGift) {
            Notification::make()
                ->title('Error')
                ->body('No se encontró el regalo para actualizar.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();
        
        // Remover campos que no pertenecen al ContentGift
        unset($data['token_name'], $data['token_id'], $data['selectedTokenId']);
        
        $this->contentGift->update($data);
        
        Notification::make()
            ->title('Regalo actualizado exitosamente')
            ->success()
            ->send();
    }

    public function resetForm(): void
    {
        if ($this->contentGift) {
            $this->form->fill($this->contentGift->toArray());
        }
    }

    public static function canAccess(): bool
    {
        return true;
    }
}