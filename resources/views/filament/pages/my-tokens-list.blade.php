<x-filament-panels::page>

        <!-- Tabla de tokens -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                {{ $this->table }}
            </div>
        </div>

        <!-- Información adicional -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex">
                <!-- Enhanced Icon -->
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        ¿Cómo usar tus tokens?
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Configurar:</strong> Personaliza el contenido de tus tokens de regalo</li>
                            <li><strong>Ver:</strong> Consulta los detalles y el estado de cada token</li>
                            <li><strong>Copiar URL:</strong> Obtén el enlace directo para compartir tu token</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>