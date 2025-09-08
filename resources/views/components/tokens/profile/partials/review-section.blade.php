@props(['token', 'content'])

@php
    // Obtener valores actuales del formulario (priorizando old() sobre datos guardados)
    $currentTitle = old('title', $content->title ?? '');
    $currentDescription = old('description', $content->description ?? '');
    $currentImageUrl = old('image_url', $content->image_url ?? '');
    
    // Información Personal - combinar old() con datos guardados
    $personalInfo = [
        'profession' => old('data.personal_info.profession', $content->data['personal_info']['profession'] ?? ''),
        'company' => old('data.personal_info.company', $content->data['personal_info']['company'] ?? ''),
        'location' => old('data.personal_info.location', $content->data['personal_info']['location'] ?? ''),
        'bio' => old('data.personal_info.bio', $content->data['personal_info']['bio'] ?? '')
    ];
    
    // Información de Contacto
    $contactInfo = [
        'phone' => old('data.contact_info.phone', $content->data['contact_info']['phone'] ?? ''),
        'email' => old('data.contact_info.email', $content->data['contact_info']['email'] ?? ''),
        'website' => old('data.contact_info.website', $content->data['contact_info']['website'] ?? ''),
        'whatsapp' => old('data.contact_info.whatsapp', $content->data['contact_info']['whatsapp'] ?? '')
    ];
    
    // Redes Sociales
    $socialLinks = [
        'linkedin' => old('data.social_links.linkedin', $content->data['social_links']['linkedin'] ?? ''),
        'twitter' => old('data.social_links.twitter', $content->data['social_links']['twitter'] ?? ''),
        'instagram' => old('data.social_links.instagram', $content->data['social_links']['instagram'] ?? ''),
        'facebook' => old('data.social_links.facebook', $content->data['social_links']['facebook'] ?? ''),
        'youtube' => old('data.social_links.youtube', $content->data['social_links']['youtube'] ?? ''),
        'tiktok' => old('data.social_links.tiktok', $content->data['social_links']['tiktok'] ?? ''),
        'telegram' => old('data.social_links.telegram', $content->data['social_links']['telegram'] ?? ''),
        'discord' => old('data.social_links.discord', $content->data['social_links']['discord'] ?? ''),
        'snapchat' => old('data.social_links.snapchat', $content->data['social_links']['snapchat'] ?? ''),
        'threads' => old('data.social_links.threads', $content->data['social_links']['threads'] ?? ''),
        'github' => old('data.social_links.github', $content->data['social_links']['github'] ?? ''),
        'spotify' => old('data.social_links.spotify', $content->data['social_links']['spotify'] ?? '')
    ];
    
    // Diseño
    $design = [
        'theme' => old('data.design.theme', $content->data['design']['theme'] ?? ''),
        'custom_colors' => [
            'primary' => old('data.design.custom_colors.primary', $content->data['design']['custom_colors']['primary'] ?? ''),
            'secondary' => old('data.design.custom_colors.secondary', $content->data['design']['custom_colors']['secondary'] ?? ''),
            'accent' => old('data.design.custom_colors.accent', $content->data['design']['custom_colors']['accent'] ?? '')
        ]
    ];
    
    $themes = [
        'corporate' => 'Corporativo',
        'creative' => 'Creativo', 
        'executive' => 'Ejecutivo',
        'modern' => 'Moderno',
        'elegant' => 'Elegante',
        'tech' => 'Tecnológico',
        'warm' => 'Cálido',
        'minimalist' => 'Minimalista',
        'corporate_dark' => 'Corporativo Dark',
        'creative_dark' => 'Creativo Dark',
        'executive_dark' => 'Ejecutivo Dark',
        'modern_dark' => 'Moderno Dark',
        'elegant_dark' => 'Elegante Dark',
        'tech_dark' => 'Tecnológico Dark',
        'warm_dark' => 'Cálido Dark',
        'minimalist_dark' => 'Minimalista Dark'
    ];
@endphp

<div class="space-y-6" id="review-section">
    <!-- Información Básica -->
    <div class="bg-white/50 backdrop-blur-sm rounded-2xl border border-kraftdo-green/20 p-6">
        <h4 class="text-lg font-semibold text-kraftdo-navy mb-4 flex items-center">
            <i class="fas fa-info-circle text-kraftdo-blue mr-2"></i>
            Información Básica
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70">Título</label>
                <p class="text-kraftdo-navy font-medium" id="review-title">{{ $currentTitle ?: 'Sin título' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70">Descripción</label>
                <p class="text-kraftdo-navy" id="review-description">{{ $currentDescription ?: 'Sin descripción' }}</p>
            </div>
            
            @if(!empty($currentImageUrl))
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-kraftdo-navy/70 mb-2">Imagen de Perfil</label>
                <div class="w-20 h-20 rounded-full bg-gray-200 overflow-hidden">
                    <img src="{{ $currentImageUrl }}" alt="Imagen de perfil" class="w-full h-full object-cover">
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Información Personal -->
    @if(!empty($personalInfo))
    <div class="bg-white/50 backdrop-blur-sm rounded-2xl border border-kraftdo-green/20 p-6">
        <h4 class="text-lg font-semibold text-kraftdo-navy mb-4 flex items-center">
            <i class="fas fa-user text-kraftdo-green mr-2"></i>
            Información Personal
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(!empty($personalInfo['profession']))
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70">Profesión</label>
                <p class="text-kraftdo-navy" id="review-profession">{{ $personalInfo['profession'] }}</p>
            </div>
            @endif
            
            @if(!empty($personalInfo['company']))
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70">Empresa</label>
                <p class="text-kraftdo-navy" id="review-company">{{ $personalInfo['company'] }}</p>
            </div>
            @endif
            
            @if(!empty($personalInfo['location']))
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70">Ubicación</label>
                <p class="text-kraftdo-navy" id="review-location">{{ $personalInfo['location'] }}</p>
            </div>
            @endif
            
            @if(!empty($personalInfo['bio']))
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-kraftdo-navy/70">Biografía</label>
                <p class="text-kraftdo-navy" id="review-bio">{{ $personalInfo['bio'] }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Información de Contacto -->
    @if(!empty($contactInfo))
    <div class="bg-white/50 backdrop-blur-sm rounded-2xl border border-kraftdo-green/20 p-6">
        <h4 class="text-lg font-semibold text-kraftdo-navy mb-4 flex items-center">
            <i class="fas fa-address-book text-kraftdo-blue mr-2"></i>
            Información de Contacto
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(!empty($contactInfo['phone']))
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70">Teléfono</label>
                <p class="text-kraftdo-navy" id="review-phone">{{ $contactInfo['phone'] }}</p>
            </div>
            @endif
            
            @if(!empty($contactInfo['email']))
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70">Email</label>
                <p class="text-kraftdo-navy" id="review-email">{{ $contactInfo['email'] }}</p>
            </div>
            @endif
            
            @if(!empty($contactInfo['website']))
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70">Sitio Web</label>
                <p class="text-kraftdo-navy" id="review-website">{{ $contactInfo['website'] }}</p>
            </div>
            @endif
            
            @if(!empty($contactInfo['whatsapp']))
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70">WhatsApp</label>
                <p class="text-kraftdo-navy" id="review-whatsapp">{{ $contactInfo['whatsapp'] }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Redes Sociales -->
    @php
        $activeSocialLinks = array_filter($socialLinks, function($value) {
            return !empty($value);
        });
    @endphp
    
    @if(!empty($activeSocialLinks))
    <div class="bg-white/50 backdrop-blur-sm rounded-2xl border border-kraftdo-green/20 p-6">
        <h4 class="text-lg font-semibold text-kraftdo-navy mb-4 flex items-center">
            <i class="fas fa-share-alt text-kraftdo-green mr-2"></i>
            Redes Sociales
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="social-links-review">
            @php
                $socialPlatforms = ['linkedin', 'twitter', 'instagram', 'facebook', 'youtube', 'tiktok', 'telegram', 'discord', 'snapchat', 'threads', 'github', 'spotify'];
                $icons = [
                    'linkedin' => 'fab fa-linkedin text-blue-600',
                    'twitter' => 'fab fa-twitter text-blue-400',
                    'instagram' => 'fab fa-instagram text-pink-600',
                    'facebook' => 'fab fa-facebook text-blue-700',
                    'youtube' => 'fab fa-youtube text-red-600',
                    'tiktok' => 'fab fa-tiktok text-black',
                    'telegram' => 'fab fa-telegram text-blue-500',
                    'discord' => 'fab fa-discord text-indigo-600',
                    'snapchat' => 'fab fa-snapchat text-yellow-400',
                    'threads' => 'fas fa-at text-black',
                    'github' => 'fab fa-github text-gray-800',
                    'spotify' => 'fab fa-spotify text-green-500'
                ];
            @endphp
            
            @foreach($socialPlatforms as $platform)
                <div class="social-link-item hidden" id="social-{{ $platform }}-review" data-platform="{{ $platform }}">
                    <div class="flex items-center space-x-3 p-3 bg-kraftdo-lime/10 rounded-xl border border-kraftdo-green/20 overflow-hidden">
                        <i class="{{ $icons[$platform] ?? 'fas fa-link text-gray-500' }} flex-shrink-0"></i>
                        <div class="min-w-0 flex-1">
                            <label class="block text-sm font-semibold text-kraftdo-navy/70 capitalize">{{ $platform }}</label>
                            <p class="text-kraftdo-navy text-sm break-all max-w-full" style="word-wrap: break-word; overflow-wrap: anywhere;" id="social-{{ $platform }}-url"></p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Diseño y Branding -->
    @if(!empty($design))
    <div class="bg-white/50 backdrop-blur-sm rounded-2xl border border-kraftdo-green/20 p-6">
        <h4 class="text-lg font-semibold text-kraftdo-navy mb-4 flex items-center">
            <i class="fas fa-palette text-kraftdo-blue mr-2"></i>
            Diseño y Branding
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if(!empty($design['theme']))
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70 mb-2">Tema Seleccionado</label>
                <p class="text-kraftdo-navy font-medium">{{ $themes[$design['theme']] ?? $design['theme'] }}</p>
            </div>
            @endif
            
            @if(!empty($design['custom_colors']))
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy/70 mb-2">Colores Personalizados</label>
                <div class="flex space-x-3">
                    @if(!empty($design['custom_colors']['primary']))
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-full border border-gray-300" style="background-color: {{ $design['custom_colors']['primary'] }}"></div>
                        <span class="text-sm text-kraftdo-navy/70">Primario</span>
                    </div>
                    @endif
                    
                    @if(!empty($design['custom_colors']['secondary']))
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-full border border-gray-300" style="background-color: {{ $design['custom_colors']['secondary'] }}"></div>
                        <span class="text-sm text-kraftdo-navy/70">Secundario</span>
                    </div>
                    @endif
                    
                    @if(!empty($design['custom_colors']['accent']))
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-full border border-gray-300" style="background-color: {{ $design['custom_colors']['accent'] }}"></div>
                        <span class="text-sm text-kraftdo-navy/70">Acento</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Mensaje de Confirmación -->
    <div class="bg-gradient-to-r from-kraftdo-lime/20 to-kraftdo-green/20 border border-kraftdo-green/30 rounded-2xl p-6">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-kraftdo-green text-2xl"></i>
            </div>
            <div>
                <h4 class="text-lg font-semibold text-kraftdo-navy">¡Listo para Guardar!</h4>
                <p class="text-kraftdo-navy/80 mt-1">
                    Revisa toda la información y haz clic en "Guardar Configuración" para actualizar tu perfil NFC.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar la revisión con datos actuales del formulario
    function updateReviewData() {
        console.log('🔄 Actualizando datos de revisión...');
        
        // Actualizar información básica
        const titleField = document.querySelector('input[name="title"]');
        const descriptionField = document.querySelector('textarea[name="description"]');
        
        if (titleField) {
            const titleReview = document.getElementById('review-title');
            if (titleReview) {
                titleReview.textContent = titleField.value || 'Sin título';
            }
        }
        
        if (descriptionField) {
            const descriptionReview = document.getElementById('review-description');
            if (descriptionReview) {
                descriptionReview.textContent = descriptionField.value || 'Sin descripción';
            }
        }
        
        // Actualizar información personal
        const personalFields = {
            'profession': 'data[personal_info][profession]',
            'company': 'data[personal_info][company]',
            'location': 'data[personal_info][location]',
            'bio': 'data[personal_info][bio]'
        };
        
        Object.keys(personalFields).forEach(field => {
            const input = document.querySelector(`input[name="${personalFields[field]}"], textarea[name="${personalFields[field]}"]`);
            const review = document.getElementById(`review-${field}`);
            
            if (input && review) {
                const value = input.value.trim();
                review.textContent = value || `Sin ${field}`;
                
                // Mostrar/ocultar la sección padre si tiene valor
                const parentDiv = review.closest('.space-y-4 > div, .grid > div');
                if (parentDiv) {
                    if (value) {
                        parentDiv.style.display = 'block';
                    } else {
                        parentDiv.style.display = 'none';
                    }
                }
            }
        });
        
        // Actualizar información de contacto
        const contactFields = {
            'phone': 'data[contact_info][phone]',
            'email': 'data[contact_info][email]',
            'website': 'data[contact_info][website]',
            'whatsapp': 'data[contact_info][whatsapp]'
        };
        
        Object.keys(contactFields).forEach(field => {
            const input = document.querySelector(`input[name="${contactFields[field]}"]`);
            const review = document.getElementById(`review-${field}`);
            
            if (input && review) {
                const value = input.value.trim();
                review.textContent = value || `Sin ${field}`;
                
                // Mostrar/ocultar la sección padre si tiene valor
                const parentDiv = review.closest('.grid > div');
                if (parentDiv) {
                    if (value) {
                        parentDiv.style.display = 'block';
                    } else {
                        parentDiv.style.display = 'none';
                    }
                }
            }
        });
        
        // Actualizar redes sociales
        const socialPlatforms = ['linkedin', 'twitter', 'instagram', 'facebook', 'youtube', 'tiktok', 'telegram', 'discord', 'snapchat', 'threads', 'github', 'spotify'];
        let visibleSocialLinks = 0;
        
        socialPlatforms.forEach(platform => {
            const input = document.querySelector(`input[name="data[social_links][${platform}]"]`);
            const reviewItem = document.getElementById(`social-${platform}-review`);
            const reviewUrl = document.getElementById(`social-${platform}-url`);
            
            if (input && reviewItem && reviewUrl) {
                const value = input.value.trim();
                if (value) {
                    reviewUrl.textContent = value;
                    reviewItem.classList.remove('hidden');
                    reviewItem.style.display = 'block';
                    visibleSocialLinks++;
                } else {
                    reviewItem.classList.add('hidden');
                    reviewItem.style.display = 'none';
                }
            }
        });
        
        // Mostrar/ocultar toda la sección de redes sociales
        const socialSection = document.querySelector('#social-links-review').closest('.bg-white\\/50');
        if (socialSection) {
            if (visibleSocialLinks > 0) {
                socialSection.style.display = 'block';
            } else {
                socialSection.style.display = 'none';
            }
        }
        
        console.log(`✅ Revisión actualizada. ${visibleSocialLinks} redes sociales visibles.`);
    }
    
    // Actualizar cuando se cambia a la pestaña de revisión
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const reviewSection = document.getElementById('review-section');
                if (reviewSection && !reviewSection.closest('.hidden')) {
                    // La sección de revisión ahora es visible
                    setTimeout(() => {
                        updateReviewData();
                    }, 100);
                }
            }
        });
    });
    
    // Observar cambios en los pasos del wizard
    const wizardSteps = document.querySelectorAll('.wizard-step-content');
    wizardSteps.forEach(step => {
        observer.observe(step, { attributes: true, attributeFilter: ['class'] });
    });
    
    // Actualizar cuando se hace clic en los pasos finales
    const reviewStepTriggers = document.querySelectorAll('[data-step="5"], .wizard-step[data-step="5"], #desktop-step-5, #mobile-indicator-5, [data-step="4"], .wizard-step[data-step="4"], #desktop-step-4, #mobile-indicator-4');
    reviewStepTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            setTimeout(() => {
                updateReviewData();
            }, 200);
        });
    });
    
    // Actualizar cada vez que cambian los campos del formulario
    const formInputs = document.querySelectorAll('input, textarea, select');
    formInputs.forEach(input => {
        input.addEventListener('input', () => {
            // Solo actualizar si estamos en el paso de revisión
            const reviewSection = document.getElementById('review-section');
            if (reviewSection && !reviewSection.closest('.hidden')) {
                setTimeout(() => {
                    updateReviewData();
                }, 100);
            }
        });
    });
    
    // Actualización inicial
    setTimeout(() => {
        updateReviewData();
    }, 500);
});
</script>
@endpush