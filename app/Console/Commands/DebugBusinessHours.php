<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContentBusiness;

class DebugBusinessHours extends Command
{
    protected $signature = 'debug:business-hours {id?}';
    protected $description = 'Debug business operating hours data';

    public function handle()
    {
        $id = $this->argument('id');
        
        if ($id) {
            $business = ContentBusiness::find($id);
            if (!$business) {
                $this->error("Business with ID {$id} not found");
                return;
            }
            $businesses = collect([$business]);
        } else {
            $businesses = ContentBusiness::whereNotNull('operating_hours')->get();
        }

        $this->info('=== BUSINESS OPERATING HOURS DEBUG ===');
        
        foreach ($businesses as $business) {
            $this->line("Business ID: {$business->id}");
            $this->line("Business Name: {$business->business_name}");
            $this->line("Operating Hours Raw: " . json_encode($business->operating_hours));
            $this->line("Operating Hours Type: " . gettype($business->operating_hours));
            $this->line("Is Array: " . (is_array($business->operating_hours) ? 'Yes' : 'No'));
            $this->line("Count: " . (is_array($business->operating_hours) ? count($business->operating_hours) : 'N/A'));
            
            if (is_array($business->operating_hours) && !empty($business->operating_hours)) {
                $this->line("First Element Type: " . gettype($business->operating_hours[0]));
                $this->line("First Element: " . json_encode($business->operating_hours[0]));
            }
            
            $formatted = $business->getFormattedOperatingHours();
            $this->line("Formatted Hours: " . json_encode($formatted));
            $this->line("Formatted Count: " . count($formatted));
            
            $this->line("---");
        }
    }
}