<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin user (idempotent - safe to re-run).
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@heydiamond.com'],
            [
                'name' => 'Admin',
                'mobile_no' => null,
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }
}
