<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminName = env('ADMIN_NAME');
        $adminPassword = env('ADMIN_PASSWORD');

        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
            ]);
        }
    }
}
