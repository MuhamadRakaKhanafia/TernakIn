<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah admin sudah ada
        $adminExists = DB::table('users')->where('email', 'admin@ternakin.com')->exists();

        if (!$adminExists) {
            DB::table('users')->insert([
                'name' => 'admin',
                'email' => 'admin@ternakin.com',
                'password' => Hash::make('password'),
                'full_name' => 'Administrator',
                'phone' => '081234567890',
                'user_type' => 'admin',
                'location_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
