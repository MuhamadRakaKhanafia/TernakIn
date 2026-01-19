<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProvinceSeeder::class,
            CitySeeder::class,
            AnimalTypeSeeder::class,
            SymptomSeeder::class,
            DiseasesSeeder::class,
            AdminSeeder::class,
            LivestockSeeder::class,
            AdditionalLivestockSeeder::class,
            // Tambahkan seeder lainnya di sini
        ]);
    }
}