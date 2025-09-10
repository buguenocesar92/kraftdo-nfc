<?php

namespace App\Filament\Pages;

use App\Models\ContentProfile;
use App\Models\ContentMultimedia;
use App\Models\ContentSocialLink;
use App\Models\ContentGalleryImage;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
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

class MyProfileTokens extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $title = 'Configurar Perfil';

    protected static ?string $navigationLabel = 'Configurar Perfil';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'my-tokens/{tokenId}/configure-profile';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];
    public ?NfcToken $token = null;
    public ?ContentProfile $contentProfile = null;
    public ?ContentMultimedia $contentMultimedia = null;
    public ?int $selectedTokenId = null;

    public function getView(): string
    {
        return 'filament.pages.my-profile-tokens';
    }

    public function getTitle(): string
    {
        if ($this->token) {
            return "Configurar Perfil #{$this->token->id}";
        }
        return 'Configurar Perfil';
    }

    public function mount($tokenId): void
    {
        // Verificar permisos antes de proceder
        if (!auth()->user()->can('configure_own_tokens')) {
            abort(403, 'No tienes permisos para configurar tokens.');
        }

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

            // Verificar que es un token de perfil
            if ($this->token->content_type !== 'PROFILE') {
                Notification::make()
                    ->title('Tipo de token incorrecto')
                    ->body('Este token no es de tipo perfil.')
                    ->warning()
                    ->send();
                $this->token = null;
                $this->selectedTokenId = null;
                return;
            }

            // Obtener o crear el ContentProfile asociado
            $this->contentProfile = $this->getOrCreateContentProfile();
            
            if ($this->contentProfile) {
                // Intentar cargar contenido multimedia (sin fallar si hay error)
                try {
                    $this->contentMultimedia = $this->getOrCreateContentMultimedia();
                } catch (\Exception $e) {
                    \Log::warning('Error loading multimedia content', ['error' => $e->getMessage()]);
                    $this->contentMultimedia = null;
                }
                
                // Combinar datos de perfil y multimedia
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
                    
                    // Asegurar que los settings se incluyen correctamente
                    if (isset($this->contentMultimedia->settings) && is_array($this->contentMultimedia->settings)) {
                        $multimediaData['settings'] = $this->contentMultimedia->settings;
                    }
                }

                // Obtener enlaces sociales
                $socialLinks = $this->contentProfile->socialLinks()
                    ->ordered()
                    ->get()
                    ->map(function ($link) {
                        return [
                            'platform' => $link->platform,
                            'url' => $link->url,
                            'username' => $link->username,
                        ];
                    })
                    ->toArray();
                
                $data = array_merge(
                    $this->contentProfile->toArray(),
                    $multimediaData,
                    [
                        'selectedTokenId' => $this->selectedTokenId,
                        'token_name' => $this->token->name,
                        'token_id' => $this->token->token_id,
                        'social_links' => $socialLinks,
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

    protected function getOrCreateContentProfile(): ?ContentProfile
    {
        if (!$this->token) {
            \Log::error('getOrCreateContentProfile: No token found');
            return null;
        }

        // Obtener el DynamicContent del token
        $dynamicContent = $this->token->dynamicContent;
        
        if (!$dynamicContent) {
            \Log::error('getOrCreateContentProfile: No dynamic content found for token', ['token_id' => $this->token->id]);
            
            // Crear DynamicContent si no existe
            try {
                $dynamicContent = DynamicContent::create([
                    'content_id' => DynamicContent::generateUniqueContentId(DynamicContent::TYPE_PROFILE),
                    'type' => DynamicContent::TYPE_PROFILE,
                    'is_active' => true,
                    'status' => 'published',
                    'nfc_token_id' => $this->token->id,
                    'user_id' => $this->token->user_id,
                    'title' => $this->token->name ?? 'Mi Perfil',
                    'description' => 'Perfil digital creado automáticamente',
                    'data' => [],
                ]);
                
                \Log::info('Created new DynamicContent for token', ['token_id' => $this->token->id, 'dynamic_content_id' => $dynamicContent->id]);
                
            } catch (\Exception $e) {
                \Log::error('Failed to create DynamicContent', [
                    'error' => $e->getMessage(),
                    'token_id' => $this->token->id,
                    'user_id' => $this->token->user_id,
                    'trace' => $e->getTraceAsString()
                ]);
                Notification::make()
                    ->title('Error de configuración')
                    ->body('No se pudo crear el contenido dinámico: ' . $e->getMessage())
                    ->danger()
                    ->send();
                return null;
            }
        }

        // Buscar o crear el ContentProfile
        try {
            $contentProfile = ContentProfile::where('dynamic_content_id', $dynamicContent->id)->first();
            
            if (!$contentProfile) {
                $contentProfile = ContentProfile::create([
                    'dynamic_content_id' => $dynamicContent->id,
                    'name' => $this->token->name ?? '',
                    'contact_email' => '',
                    'contact_phone' => '',
                    'contact_website' => '',
                    'bio' => '',
                ]);
                
                \Log::info('Created new ContentProfile', ['profile_id' => $contentProfile->id, 'dynamic_content_id' => $dynamicContent->id]);
            }
            
            return $contentProfile;
            
        } catch (\Exception $e) {
            \Log::error('Failed to create ContentProfile', ['error' => $e->getMessage(), 'dynamic_content_id' => $dynamicContent->id]);
            Notification::make()
                ->title('Error de base de datos')
                ->body('No se pudo crear o encontrar el perfil: ' . $e->getMessage())
                ->danger()
                ->send();
            return null;
        }
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
                Section::make('Información del Perfil')
                    ->description('Configura la información básica de tu perfil')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->placeholder('Tu nombre completo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Textarea::make('bio')
                            ->label('Biografía')
                            ->placeholder('Cuéntanos sobre ti...')
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        TextInput::make('contact_email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->placeholder('tu@email.com'),
                        
                        TextInput::make('contact_phone')
                            ->label('Teléfono')
                            ->placeholder('+1 234 567 8900'),
                        
                        TextInput::make('contact_website')
                            ->label('Sitio Web')
                            ->url()
                            ->placeholder('https://tu-sitio.com')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Enlaces Sociales')
                    ->description('Agrega tus redes sociales y enlaces importantes')
                    ->schema([
                        Repeater::make('social_links')
                            ->label('Enlaces Sociales')
                            ->schema([
                                Select::make('platform')
                                    ->label('Plataforma')
                                    ->options([
                                        'instagram' => '📷 Instagram',
                                        'linkedin' => '💼 LinkedIn',
                                        'twitter' => '🐦 Twitter/X',
                                        'facebook' => '📘 Facebook',
                                        'tiktok' => '🎵 TikTok',
                                        'youtube' => '📹 YouTube',
                                        'github' => '💻 GitHub',
                                        'website' => '🌐 Sitio Web',
                                    ])
                                    ->required()
                                    ->columnSpan(1),
                                
                                TextInput::make('username')
                                    ->label('Usuario/Nombre')
                                    ->placeholder('tu_usuario')
                                    ->columnSpan(1),
                                
                                TextInput::make('url')
                                    ->label('URL Completa (opcional)')
                                    ->url()
                                    ->placeholder('https://...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->defaultItems(0)
                            ->maxItems(8)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Contenido Multimedia')
                    ->description('Agrega contenido visual a tu perfil')
                    ->schema([
                        // Imagen de perfil principal
                        FileUpload::make('settings.profile_image')
                            ->label('Imagen de Perfil')
                            ->directory('profiles')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(5 * 1024) // 5MB
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->helperText('Sube una imagen para tu perfil. Se mostrará en forma circular.')
                            ->columnSpan(1),

                        // Video de presentación
                        Select::make('video_type')
                            ->label('Tipo de video de presentación')
                            ->options([
                                'direct' => 'URL externa',
                                'file_upload' => 'Archivo subido',
                                'youtube' => 'YouTube',
                                'vimeo' => 'Vimeo',
                            ])
                            ->default('direct')
                            ->columnSpan(1),
                        
                        TextInput::make('video_url')
                            ->label('URL del video de presentación')
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->url()
                            ->columnSpanFull(),
                        
                        FileUpload::make('video_file')
                            ->label('Archivo de video de presentación')
                            ->directory('profiles/videos')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->maxSize(50 * 1024) // 50MB
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Galería de Imágenes')
                    ->description('Agrega imágenes adicionales a tu perfil')
                    ->schema([
                        FileUpload::make('gallery_images')
                            ->label('Galería de Imágenes')
                            ->directory('profiles/gallery')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(10 * 1024) // 10MB por imagen
                            ->maxFiles(12)
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
            ->model(ContentProfile::class);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('update')
                ->label('Actualizar Perfil')
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
        // Asegurar que existe el perfil antes de actualizar
        if (!$this->contentProfile) {
            $this->contentProfile = $this->getOrCreateContentProfile();
        }

        if (!$this->contentProfile) {
            Notification::make()
                ->title('Error')
                ->body('No se pudo crear o encontrar el perfil para actualizar.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();
        
        // Separar datos por modelo
        $profileData = [
            'name' => $data['name'] ?? '',
            'bio' => $data['bio'] ?? '',
            'contact_email' => $data['contact_email'] ?? '',
            'contact_phone' => $data['contact_phone'] ?? '',
            'contact_website' => $data['contact_website'] ?? '',
        ];
        
        // Actualizar ContentProfile
        $this->contentProfile->update($profileData);
        
        // Crear o actualizar ContentMultimedia si hay datos multimedia
        if (!$this->contentMultimedia) {
            $this->contentMultimedia = $this->getOrCreateContentMultimedia();
        }
        
        if ($this->contentMultimedia) {
            $multimediaData = [
                'video_url' => $data['video_url'] ?? null,
                'video_file' => $data['video_file'] ?? null,
                'video_type' => $data['video_type'] ?? 'direct',
                'settings' => array_merge($this->contentMultimedia->settings ?? [], $data['settings'] ?? []),
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
                            'alt_text' => "Imagen de perfil " . ($index + 1),
                        ]);
                    }
                }
            }
        }

        // Manejar enlaces sociales
        if (isset($data['social_links']) && is_array($data['social_links'])) {
            // Eliminar enlaces existentes
            $this->contentProfile->socialLinks()->delete();
            
            // Crear nuevos enlaces
            foreach ($data['social_links'] as $index => $link) {
                if (!empty($link['platform'])) {
                    ContentSocialLink::create([
                        'dynamic_content_id' => $this->contentProfile->dynamic_content_id,
                        'platform' => $link['platform'],
                        'username' => $link['username'] ?? '',
                        'url' => $link['url'] ?? '',
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }
        
        Notification::make()
            ->title('Perfil actualizado exitosamente')
            ->body('Se ha guardado toda la información del perfil.')
            ->success()
            ->send();
    }

    public function resetForm(): void
    {
        if ($this->contentProfile) {
            $this->form->fill($this->contentProfile->toArray());
        }
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('configure_own_tokens');
    }
}