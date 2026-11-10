<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@accountai.com'],
            [
                'name' => 'AccountAI Administrator',
                'phone' => '+44 20 7946 0991',
                'company' => 'AccountAI Practice HQ',
                'password' => Hash::make('admin@123'),
                'role' => 'admin',
            ]
        );
    }
}
