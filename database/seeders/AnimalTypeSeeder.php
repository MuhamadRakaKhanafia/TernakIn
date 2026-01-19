<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AnimalTypeSeeder extends Seeder
{
    public function run()
    {
        // Skip jika tabel tidak ada
        if (!Schema::hasTable('animal_types')) {
            return;
        }

        $animalTypes = [
            [
                'name' => 'Ayam',
                'description' => 'Unggas domestik untuk produksi telur dan daging',
                'category' => 'poultry',
            ],
            [
                'name' => 'Sapi',
                'description' => 'Ternak besar untuk produksi daging dan susu',
                'category' => 'large_animal',
            ],
            [
                'name' => 'Kambing',
                'description' => 'Ternak kecil untuk produksi daging dan susu',
                'category' => 'large_animal',
            ],
            [
                'name' => 'Domba',
                'description' => 'Ternak kecil untuk produksi daging dan wol',
                'category' => 'large_animal',
            ],
            [
                'name' => 'Bebek',
                'description' => 'Unggas air untuk produksi telur dan daging',
                'category' => 'poultry',
            ],
            [
                'name' => 'Kelinci',
                'description' => 'Hewan ternak kecil untuk produksi daging',
                'category' => 'other',
            ],
        ];

        $inserted = 0;
        $skipped = 0;

        foreach ($animalTypes as $type) {
            // Cek apakah sudah ada
            $exists = DB::table('animal_types')
                ->where('name', $type['name'])
                ->exists();

            if (!$exists) {
                DB::table('animal_types')->insert(array_merge($type, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $inserted++;
            } else {
                $skipped++;
            }
        }

    }
}