<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentBusinessGroup;

class BusinessGroupGoogleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el primer business group disponible
        $businessGroup = ContentBusinessGroup::first();
        
        if ($businessGroup) {
            $businessGroup->update([
                'google_place_id' => 'ChIJN1YGj8fBYpYRpVEG8L8m1k8', // Ejemplo de Place ID
                'google_reviews_url' => 'https://g.page/r/CRGlUSHygt8EEAE/review', // Ejemplo de URL de reseñas
            ]);
            
            $this->command->info("✅ Agregados datos de Google al Business Group: {$businessGroup->group_name}");
        } else {
            $this->command->warn("⚠️ No se encontraron Business Groups para actualizar");
        }
        
        // Si hay más de un business group, agregar datos a varios
        $otherBusinessGroups = ContentBusinessGroup::skip(1)->take(2)->get();
        
        foreach ($otherBusinessGroups as $group) {
            $group->update([
                'google_place_id' => 'ChIJ' . str_replace('-', '', substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 15)),
                'google_reviews_url' => 'https://g.page/r/' . str_replace('-', '', substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 12)) . '/review',
            ]);
            
            $this->command->info("✅ Agregados datos de Google al Business Group: {$group->group_name}");
        }
    }
}
