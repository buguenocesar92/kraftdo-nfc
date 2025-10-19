<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@kraftdo-nfc.com'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Super Admin role
        if (! $superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole('Super Admin');
        }

        // Create other test users
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        $editor = User::updateOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $editor->hasRole('Editor')) {
            $editor->assignRole('Editor');
        }

        $viewer = User::updateOrCreate(
            ['email' => 'viewer@example.com'],
            [
                'name' => 'Viewer User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $viewer->hasRole('Viewer')) {
            $viewer->assignRole('Viewer');
        }

        $contentManager = User::updateOrCreate(
            ['email' => 'content@example.com'],
            [
                'name' => 'Content Manager',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $contentManager->hasRole('Content Manager')) {
            $contentManager->assignRole('Content Manager');
        }

        // Create NFC test user
        $nfcUser = User::updateOrCreate(
            ['email' => 'nfc@example.com'],
            [
                'name' => 'NFC Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $nfcUser->hasRole('NFC')) {
            $nfcUser->assignRole('NFC');
        }

        $this->command->info('Admin users created successfully!');
        $this->command->info('Super Admin: admin@kraftdo-nfc.com / password');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Editor: editor@example.com / password');
        $this->command->info('Viewer: viewer@example.com / password');
        $this->command->info('Content Manager: content@example.com / password');
        $this->command->info('NFC User: nfc@example.com / password');
    }
}
