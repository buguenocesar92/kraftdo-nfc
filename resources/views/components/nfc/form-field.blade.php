@props([
    'type' => 'text',
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'icon' => null,
    'showPassword' => false,
    'value' => '',
    'error' => null,
    'maxlength' => null,
    'validation' => null // Alpine.js validation expression
])

<div class="kraftdo-form-field" x-data="kraftdoFormField('{{ $name }}', '{{ $type }}')">
    <!-- Label -->
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-white mb-2">
            @if($icon)
                <i class="{{ $icon }} mr-2"></i>
            @endif
            {{ $label }}
            @if($required)
                <span class="text-red-300">*</span>
            @endif
        </label>
    @endif
    
    <!-- Input Container -->
    <div class="relative">
        @if($type === 'password')
            <!-- Password Field -->
            <input :type="showPassword ? 'text' : 'password'"
                   id="{{ $name }}"
                   name="{{ $name }}"
                   value="{{ old($name, $value) }}"
                   @if($required) required @endif
                   @if($maxlength) maxlength="{{ $maxlength }}" @endif
                   @if($validation) x-model="value" @endif
                   class="w-full px-4 py-3 pr-12 kraftdo-glass border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all backdrop-blur-sm"
                   :class="hasError ? 'border-red-400 focus:ring-red-400/50' : ''"
                   placeholder="{{ $placeholder }}"
                   x-on:input="validateField()"
                   {{ $attributes }}>
            
            <!-- Password Toggle -->
            <button type="button" 
                    @click="togglePasswordVisibility()"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/70 hover:text-white transition-colors">
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
            
        @elseif($type === 'checkbox')
            <!-- Checkbox Field -->
            <label class="flex items-start space-x-3 cursor-pointer">
                <input type="checkbox" 
                       id="{{ $name }}"
                       name="{{ $name }}"
                       value="1"
                       @if($required) required @endif
                       @if(old($name, $value)) checked @endif
                       class="mt-1 w-5 h-5 bg-white/20 border border-white/30 rounded focus:ring-2 focus:ring-white/50 text-indigo-600 transition-all">
                <span class="text-sm text-white/80">
                    {{ $slot }}
                </span>
            </label>
            
        @else
            <!-- Regular Input Field -->
            <input type="{{ $type }}"
                   id="{{ $name }}"
                   name="{{ $name }}"
                   value="{{ old($name, $value) }}"
                   @if($required) required @endif
                   @if($maxlength) maxlength="{{ $maxlength }}" @endif
                   @if($validation) x-model="value" @endif
                   class="w-full px-4 py-3 kraftdo-glass border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all backdrop-blur-sm"
                   :class="hasError ? 'border-red-400 focus:ring-red-400/50' : ''"
                   placeholder="{{ $placeholder }}"
                   x-on:input="validateField()"
                   {{ $attributes }}>
        @endif
    </div>
    
    <!-- Validation Messages -->
    @if($validation)
        <div x-show="validationMessage" class="mt-2 flex items-center text-sm" 
             :class="isValid ? 'text-green-300' : 'text-red-300'">
            <i :class="isValid ? 'fas fa-check mr-2' : 'fas fa-times mr-2'"></i>
            <span x-text="validationMessage"></span>
        </div>
    @endif
    
    <!-- Laravel Validation Errors -->
    @if($error || $errors->has($name))
        <p class="mt-2 text-sm text-red-300 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ $error ?? $errors->first($name) }}
        </p>
    @endif
    
    <!-- Helper Text -->
    @if($type === 'password' && !$error && !$errors->has($name))
        <p class="mt-1 text-xs text-white/60">Mínimo 8 caracteres</p>
    @endif
</div>

<script>
    function kraftdoFormField(name, type) {
        return {
            name: name,
            type: type,
            showPassword: false,
            value: '',
            isValid: null,
            hasError: false,
            validationMessage: '',
            
            init() {
                if (this.type === 'password') {
                    this.setupPasswordValidation();
                }
            },
            
            togglePasswordVisibility() {
                this.showPassword = !this.showPassword;
            },
            
            validateField() {
                // Basic validation - can be extended
                if (this.type === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    this.isValid = emailRegex.test(this.value);
                    this.validationMessage = this.isValid ? 'Email válido' : 'Formato de email inválido';
                } else if (this.type === 'password') {
                    this.validatePassword();
                }
                
                this.hasError = this.isValid === false;
            },
            
            validatePassword() {
                const length = this.value.length;
                if (length === 0) {
                    this.isValid = null;
                    this.validationMessage = '';
                } else if (length < 8) {
                    this.isValid = false;
                    this.validationMessage = `Necesitas ${8 - length} caracteres más`;
                } else {
                    this.isValid = true;
                    this.validationMessage = 'Contraseña válida';
                }
            },
            
            setupPasswordValidation() {
                this.$watch('value', () => {
                    this.validatePassword();
                });
            }
        }
    }
</script>