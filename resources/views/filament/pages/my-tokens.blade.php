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
    </div>
</x-filament-panels::page>