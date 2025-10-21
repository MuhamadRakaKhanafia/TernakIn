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
            DiseasesSeeder::class,
            DiseaseRelationsSeeder::class
            // Tambahkan seeder lainnya di sini
        ]);
    }
}