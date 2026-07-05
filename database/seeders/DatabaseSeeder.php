<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => env('MAILFLOW_DEFAULT_ADMIN_EMAIL', 'admin@example.com'),
        ], [
            'name' => 'MailFlow Admin',
            'role' => User::ROLE_SUPER_ADMIN,
            'password' => Hash::make(env('MAILFLOW_DEFAULT_ADMIN_PASSWORD', 'password')),
            'email_verified_at' => now(),
        ]);
    }
}
