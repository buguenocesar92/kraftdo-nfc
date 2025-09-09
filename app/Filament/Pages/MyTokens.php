<?php

namespace App\Filament\Pages;

use App\Models\ContentGift;
use App\Models\ContentMultimedia;
use App\Models\ContentGalleryImage;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use Filament\Forms\Components\FileUpload;
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
    public ?ContentMultimedia $contentMultimedia = null;
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
                // Intentar cargar contenido multimedia (sin fallar si hay error)
                try {
                    $this->contentMultimedia = $this->getOrCreateContentMultimedia();
                } catch (\Exception $e) {
                    \Log::warning('Error loading multimedia content', ['error' => $e->getMessage()]);
                    $this->contentMultimedia = null;
                }
                
                // Combinar datos de regalo y multimedia
                $multimediaData = $this->contentMultimedia ? $this->contentMultimedia->toArray() : [];
                
                // Agregar imágenes de galería si existen
                if ($this->contentMultimedia) {
                    $galleryImages = $this->contentMultimedia->galleryImages()
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->pluck('image_path')
                        ->filter()
                        ->values()
                        ->toArray();
                    $multimediaData['gallery_images'] = $galleryImages;
                }
                
                $data = array_merge(
                    $this->contentGift->toArray(),
                    $multimediaData,
                    [
                        'selectedTokenId' => $this->selectedTokenId,
                        'token_name' => $this->token->name,
                        'token_id' => $this->token->token_id,
                    ]
                );
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

    protected function getOrCreateContentMultimedia(): ?ContentMultimedia
    {
        if (!$this->token) {
            return null;
        }

        // Obtener el DynamicContent del token
        $dynamicContent = $this->token->dynamicContent;
        
        if (!$dynamicContent) {
            return null;
        }

        // Buscar o crear el ContentMultimedia
        $contentMultimedia = ContentMultimedia::where('dynamic_content_id', $dynamicContent->id)->first();
        
        if (!$contentMultimedia) {
            $contentMultimedia = ContentMultimedia::create([
                'dynamic_content_id' => $dynamicContent->id,
                'video_url' => null,
                'video_file' => null,
                'video_type' => 'direct',
                'audio_url' => null,
                'audio_file' => null,
                'audio_type' => 'direct',
                'settings' => [],
            ]);
        }

        return $contentMultimedia;
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

                Section::make('Contenido Multimedia')
                    ->description('Agrega video y audio a tu regalo')
                    ->schema([
                        // Video
                        Select::make('video_type')
                            ->label('Tipo de video')
                            ->options([
                                'direct' => 'URL externa',
                                'file_upload' => 'Archivo subido',
                                'youtube' => 'YouTube',
                                'vimeo' => 'Vimeo',
                            ])
                            ->default('direct')
                            ->live()
                            ->columnSpan(1),
                        
                        TextInput::make('video_url')
                            ->label('URL del video')
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->url()
                            ->visible(fn ($get) => in_array($get('video_type'), ['direct', 'youtube', 'vimeo']))
                            ->columnSpan(1),
                        
                        FileUpload::make('video_file')
                            ->label('Archivo de video')
                            ->directory('videos')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->maxSize(50 * 1024) // 50MB
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->visible(fn ($get) => $get('video_type') === 'file_upload')
                            ->columnSpan(1),

                        // Audio
                        Select::make('audio_type')
                            ->label('Tipo de audio')
                            ->options([
                                'direct' => 'URL externa',
                                'file_upload' => 'Archivo subido',
                                'youtube_music' => 'YouTube Music',
                                'spotify' => 'Spotify',
                                'soundcloud' => 'SoundCloud',
                            ])
                            ->default('direct')
                            ->live()
                            ->columnSpan(1),
                        
                        TextInput::make('audio_url')
                            ->label('URL del audio')
                            ->placeholder('https://example.com/audio.mp3')
                            ->url()
                            ->visible(fn ($get) => in_array($get('audio_type'), ['direct', 'youtube_music', 'spotify', 'soundcloud']))
                            ->columnSpan(1),
                        
                        FileUpload::make('audio_file')
                            ->label('Archivo de audio')
                            ->directory('audio')
                            ->acceptedFileTypes(['audio/mp3', 'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/x-m4a', 'audio/aac'])
                            ->maxSize(20 * 1024) // 20MB
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->visible(fn ($get) => $get('audio_type') === 'file_upload')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Galería de Imágenes')
                    ->description('Agrega imágenes a tu regalo')
                    ->schema([
                        FileUpload::make('gallery_images')
                            ->label('Imágenes')
                            ->directory('gallery')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(10 * 1024) // 10MB por imagen
                            ->maxFiles(10)
                            ->multiple()
                            ->reorderable()
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->imageEditor()
                            ->imagePreviewHeight('150')
                            ->panelLayout('grid')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
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
        
        // Separar datos por modelo
        $giftData = [
            'sender_name' => $data['sender_name'] ?? '',
            'recipient_name' => $data['recipient_name'] ?? '',
            'message' => $data['message'] ?? '',
        ];
        
        // Actualizar ContentGift
        $this->contentGift->update($giftData);
        
        // Crear o actualizar ContentMultimedia si hay datos multimedia
        if (!$this->contentMultimedia) {
            $this->contentMultimedia = $this->getOrCreateContentMultimedia();
        }
        
        if ($this->contentMultimedia) {
            $multimediaData = [
                'video_url' => $data['video_url'] ?? null,
                'video_file' => $data['video_file'] ?? null,
                'video_type' => $data['video_type'] ?? 'direct',
                'audio_url' => $data['audio_url'] ?? null,
                'audio_file' => $data['audio_file'] ?? null,
                'audio_type' => $data['audio_type'] ?? 'direct',
                'settings' => $data['settings'] ?? [],
            ];
            
            $this->contentMultimedia->update($multimediaData);
            
            // Manejar galería de imágenes
            if (isset($data['gallery_images']) && is_array($data['gallery_images'])) {
                // Eliminar imágenes existentes
                $this->contentMultimedia->galleryImages()->delete();
                
                // Crear nuevas imágenes
                foreach ($data['gallery_images'] as $index => $imagePath) {
                    if ($imagePath) {
                        ContentGalleryImage::create([
                            'content_multimedia_id' => $this->contentMultimedia->id,
                            'image_path' => $imagePath,
                            'image_url' => null,
                            'type' => ContentGalleryImage::TYPE_UPLOAD,
                            'sort_order' => $index + 1,
                            'alt_text' => "Imagen " . ($index + 1),
                        ]);
                    }
                }
            }
        }
        
        Notification::make()
            ->title('Contenido actualizado exitosamente')
            ->body('Se ha guardado toda la información del regalo y multimedia.')
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