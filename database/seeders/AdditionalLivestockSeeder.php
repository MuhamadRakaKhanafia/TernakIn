<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Livestock;
use App\Models\User;

class AdditionalLivestockSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'user_type' => 'peternak'
            ]);
        }

        // Add sheep (Domba)
        Livestock::updateOrCreate(
            ['identification_number' => 'DM001'],
            [
                'user_id' => $user->id,
                'animal_type_id' => 4, // Domba
                'name' => 'Domba Putih 001',
                'birth_date' => '2023-05-15',
                'acquisition_date' => '2023-06-01',
                'sex' => 'betina',
                'age_months' => 19,
                'weight_kg' => 35.0,
                'health_status' => 'sehat',
                'vaccination_status' => 'up_to_date',
                'breed' => 'Merino',
                'purpose' => 'daging',
                'feed_type' => 'Rumput',
                'daily_feed_kg' => 1.5,
                'housing_type' => 'Padang rumput',
                'milk_production_liter' => 0,
                'pregnancy_status' => 'tidak_hamil',
                'notes' => 'Domba sehat'
            ]
        );

        // Add duck (Bebek)
        Livestock::updateOrCreate(
            ['identification_number' => 'BK001'],
            [
                'user_id' => $user->id,
                'animal_type_id' => 5, // Bebek
                'name' => 'Bebek Peking 001',
                'birth_date' => '2024-10-01',
                'acquisition_date' => '2024-10-15',
                'sex' => 'betina',
                'age_weeks' => 8,
                'weight_kg' => 2.5,
                'health_status' => 'sehat',
                'vaccination_status' => 'up_to_date',
                'strain' => 'Peking',
                'feed_type' => 'Pakan bebek',
                'daily_feed_kg' => 0.2,
                'housing_type' => 'Kandang bebek',
                'egg_production' => 150,
                'flock_size' => 50,
                'notes' => 'Bebek produktif'
            ]
        );

        // Add rabbit (Kelinci)
        Livestock::updateOrCreate(
            ['identification_number' => 'KL001'],
            [
                'user_id' => $user->id,
                'animal_type_id' => 6, // Kelinci
                'name' => 'Kelinci Flemish 001',
                'birth_date' => '2024-07-01',
                'acquisition_date' => '2024-07-15',
                'sex' => 'betina',
                'age_months' => 5,
                'weight_kg' => 3.2,
                'health_status' => 'sehat',
                'vaccination_status' => 'up_to_date',
                'breed' => 'Flemish Giant',
                'purpose' => 'daging',
                'feed_type' => 'Pelet kelinci',
                'daily_feed_kg' => 0.15,
                'housing_type' => 'Kandang kelinci',
                'milk_production_liter' => 0,
                'pregnancy_status' => 'tidak_hamil',
                'notes' => 'Kelinci sehat'
            ]
        );
    }
}
