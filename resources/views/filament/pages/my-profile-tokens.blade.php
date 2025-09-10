<x-filament-panels::page>
    <div class="space-y-6">
    <!-- Formulario de actualización -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                
                <form wire:submit="update">
                    {{ $this->form }}
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        @foreach ($this->getFormActions() as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                </form>
            </div>
        </div>

        <!-- Información adicional sobre perfiles -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        💡 Consejos para tu perfil
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Biografía:</strong> Mantén una descripción concisa y atractiva</li>
                            <li><strong>Enlaces sociales:</strong> Agrega tus redes más importantes</li>
                            <li><strong>Imagen de perfil:</strong> Use una foto clara y profesional</li>
                            <li><strong>Información de contacto:</strong> Facilita que te encuentren</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>