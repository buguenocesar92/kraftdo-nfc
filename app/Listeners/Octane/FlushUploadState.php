<?php

namespace App\Listeners\Octane;

use Laravel\Octane\Contracts\OperationTerminated;

class FlushUploadState implements OperationTerminated
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        // Limpiar archivos temporales de uploads que puedan haberse quedado en memoria
        if (session()->has('temp_upload_paths')) {
            $paths = session()->get('temp_upload_paths', []);
            
            foreach ($paths as $path) {
                if (file_exists($path) && str_contains($path, 'temp') && str_contains($path, storage_path())) {
                    @unlink($path);
                }
            }
            
            session()->forget('temp_upload_paths');
        }
        
        // Limpiar cualquier buffer de upload en memoria
        if (session()->has('upload_buffers')) {
            session()->forget('upload_buffers');
        }
        
        // Force cleanup de variables grandes relacionadas con multimedia
        if (isset($GLOBALS['nfc_multimedia_buffer'])) {
            unset($GLOBALS['nfc_multimedia_buffer']);
        }
        
        // Limpiar directorio temporal de Octane si existe
        $octaneTmpDir = storage_path('app/octane-uploads');
        if (is_dir($octaneTmpDir)) {
            $files = glob($octaneTmpDir . '/*');
            $now = time();
            
            foreach ($files as $file) {
                // Eliminar archivos temporales más antiguos que 1 hora
                if (filemtime($file) < ($now - 3600)) {
                    @unlink($file);
                }
            }
        }
    }
}