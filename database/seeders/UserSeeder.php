<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin HR',
            'email' => 'admin@perusahaan.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'leave_quota' => 12,
        ]);

        User::create([
            'name' => 'Asep Jamaludin',
            'email' => 'asep@seal.com',
            'password' => Hash::make('password123'),
            'role' => 'employee',
            'leave_quota' => 12,
        ]);
    }
}