<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@clinicalsanctuary.com'],
            [
                'first_name' => 'Admin',
                'last_name'  => 'Principal',
                'password'   => Hash::make('test2026'),
                'role_key'   => 'admin',
                'is_active'  => 1,
            ]
        );
    }
}
