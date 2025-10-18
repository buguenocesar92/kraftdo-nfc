<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckDatabaseSchema extends Command
{
    protected $signature = 'debug:check-schema';
    protected $description = 'Check database schema for content_businesses table';

    public function handle()
    {
        $this->info('=== DATABASE SCHEMA CHECK ===');
        
        // Check if table exists
        if (!Schema::hasTable('content_businesses')) {
            $this->error('❌ Table content_businesses does not exist!');
            return;
        }
        
        $this->info('✅ Table content_businesses exists');
        
        // Get table columns
        $columns = Schema::getColumnListing('content_businesses');
        $this->info('📋 Table columns:');
        foreach ($columns as $column) {
            $this->line("   - {$column}");
        }
        
        // Check specific fields
        $requiredFields = ['latitude', 'longitude', 'operating_hours', 'address'];
        $this->info('🔍 Checking required fields:');
        
        foreach ($requiredFields as $field) {
            if (Schema::hasColumn('content_businesses', $field)) {
                $this->info("   ✅ {$field} - EXISTS");
            } else {
                $this->error("   ❌ {$field} - MISSING");
            }
        }
        
        // Check column types
        $this->info('📊 Column details:');
        $columnInfo = DB::select("DESCRIBE content_businesses");
        foreach ($columnInfo as $column) {
            if (in_array($column->Field, $requiredFields)) {
                $this->line("   {$column->Field}: {$column->Type} (Null: {$column->Null}, Default: {$column->Default})");
            }
        }
        
        // Count records
        $count = DB::table('content_businesses')->count();
        $this->info("📈 Total records: {$count}");
        
        // Check for records with missing data
        $withoutLocation = DB::table('content_businesses')
            ->whereNull('latitude')
            ->orWhereNull('longitude')
            ->count();
        
        $withoutHours = DB::table('content_businesses')
            ->whereNull('operating_hours')
            ->count();
            
        $this->info("📊 Records without location: {$withoutLocation}");
        $this->info("📊 Records without hours: {$withoutHours}");
        
        // Show sample data
        $sample = DB::table('content_businesses')->first();
        if ($sample) {
            $this->info('📄 Sample record:');
            $this->line('   ID: ' . $sample->id);
            $this->line('   Name: ' . $sample->business_name);
            $this->line('   Address: ' . ($sample->address ?? 'NULL'));
            $this->line('   Latitude: ' . ($sample->latitude ?? 'NULL'));
            $this->line('   Longitude: ' . ($sample->longitude ?? 'NULL'));
            $this->line('   Operating Hours: ' . ($sample->operating_hours ?? 'NULL'));
        }
    }
}