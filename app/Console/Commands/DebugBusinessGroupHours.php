<?php

namespace App\Console\Commands;

use App\Models\ContentBusinessGroup;
use Illuminate\Console\Command;

class DebugBusinessGroupHours extends Command
{
    protected $signature = 'debug:business-group-hours {id?}';
    protected $description = 'Debug business group operating hours data';

    public function handle()
    {
        $id = $this->argument('id') ?? 1;

        $businessGroup = ContentBusinessGroup::find($id);
        if (! $businessGroup) {
            $this->error("Business Group with ID {$id} not found");

            return;
        }

        $this->info('=== BUSINESS GROUP OPERATING HOURS DEBUG ===');
        $this->line("Group ID: {$businessGroup->id}");
        $this->line("Group Name: {$businessGroup->group_name}");

        // Raw database value
        $rawValue = $businessGroup->getOriginal('operating_hours');
        $this->line("Raw DB Value: " . json_encode($rawValue));
        $this->line("Raw DB Type: " . gettype($rawValue));

        // Casted value
        $castedValue = $businessGroup->operating_hours;
        $this->line("Casted Value: " . json_encode($castedValue));
        $this->line("Casted Type: " . gettype($castedValue));

        // Test manual update
        $this->line("\n--- Testing Manual Update ---");
        $testData = [
            'monday' => '09:00-18:00',
            'tuesday' => '09:00-18:00',
        ];

        $businessGroup->operating_hours = $testData;
        $businessGroup->save();

        $this->line("After update - Raw: " . json_encode($businessGroup->getOriginal('operating_hours')));
        $this->line("After update - Casted: " . json_encode($businessGroup->operating_hours));
    }
}
