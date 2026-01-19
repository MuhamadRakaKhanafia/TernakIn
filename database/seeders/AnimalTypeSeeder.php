<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AnimalTypeSeeder extends Seeder
{
    public function run()
    {
        if (!Schema::hasTable('animal_types')) {
            return;
        }

        $animalTypes = [
            [
                'name' => 'Ayam',
            ],
            [
                'name' => 'Sapi',
            ],
            [
                'name' => 'Kambing',
            ],
            [
                'name' => 'Domba',

            ],
            [
                'name' => 'Bebek',
            ],
            [
                'name' => 'Kelinci',
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