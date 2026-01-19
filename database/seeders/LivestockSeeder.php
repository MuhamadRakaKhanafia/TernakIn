<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Livestock;
use App\Models\User;

class LivestockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user or create one if none exists
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'user_type' => 'user'
            ]);
        }

        // Sample livestock data with ALL fields filled
        $livestocks = [
            [
                'user_id' => $user->id,
                'animal_type_id' => 1, // Assuming Sapi (Cow)
                'name' => 'Sapi Betina 001',
                'identification_number' => 'SP001',
                'birth_date' => '2022-01-15',
                'acquisition_date' => '2023-01-15',
                'sex' => 'betina',
                'age_months' => 24,
                'weight_kg' => 450.5,
                'health_status' => 'sehat',
                'vaccination_status' => 'up_to_date',
                'breed' => 'Limousin',
                'purpose' => 'daging',
                'feed_type' => 'Rumput dan konsentrat',
                'daily_feed_kg' => 15.5,
                'housing_type' => 'Kandang terbuka',
                'milk_production_liter' => 0,
                'pregnancy_status' => 'tidak_hamil',
                'notes' => 'Sapi sehat dan produktif'
            ],
            [
                'user_id' => $user->id,
                'animal_type_id' => 1, // Sapi
                'name' => 'Sapi Jantan 002',
                'identification_number' => 'SP002',
                'birth_date' => '2021-06-10',
                'acquisition_date' => '2022-06-10',
                'sex' => 'jantan',
                'age_months' => 36,
                'weight_kg' => 520.0,
                'health_status' => 'sehat',
                'vaccination_status' => 'up_to_date',
                'breed' => 'Brahman',
                'purpose' => 'daging',
                'feed_type' => 'Rumput hijau',
                'daily_feed_kg' => 18.0,
                'housing_type' => 'Padang rumput',
                'milk_production_liter' => 0,
                'pregnancy_status' => null,
                'notes' => 'Sapi jantan untuk breeding'
            ],
            [
                'user_id' => $user->id,
                'animal_type_id' => 2, // Assuming Ayam (Chicken)
                'name' => 'Ayam Petelur 001',
                'identification_number' => 'AY001',
                'birth_date' => '2024-08-01',
                'acquisition_date' => '2024-08-15',
                'sex' => 'betina',
                'age_weeks' => 12,
                'weight_kg' => 1.8,
                'health_status' => 'sehat',
                'vaccination_status' => 'up_to_date',
                'strain' => 'Isa Brown',
                'feed_type' => 'Pakan ayam petelur',
                'daily_feed_kg' => 0.12,
                'housing_type' => 'Kandang baterai',
                'egg_production' => 280,
                'flock_size' => 500,
                'notes' => 'Ayam petelur produktif'
            ],
            [
                'user_id' => $user->id,
                'animal_type_id' => 2, // Ayam
                'name' => 'Ayam Pedaging 002',
                'identification_number' => 'AY002',
                'birth_date' => '2024-09-01',
                'acquisition_date' => '2024-09-10',
                'sex' => 'jantan',
                'age_weeks' => 8,
                'weight_kg' => 2.1,
                'health_status' => 'sehat',
                'vaccination_status' => 'need_update',
                'strain' => 'Cobb 500',
                'feed_type' => 'Pakan ayam pedaging',
                'daily_feed_kg' => 0.15,
                'housing_type' => 'Kandang broiler',
                'egg_production' => 0,
                'flock_size' => 1000,
                'notes' => 'Ayam pedaging siap panen'
            ],
            [
                'user_id' => $user->id,
                'animal_type_id' => 3, // Assuming Kambing (Goat)
                'name' => 'Kambing Perah 003',
                'identification_number' => 'KB003',
                'birth_date' => '2023-03-20',
                'acquisition_date' => '2023-04-01',
                'sex' => 'betina',
                'age_months' => 18,
                'weight_kg' => 45.0,
                'health_status' => 'sakit',
                'vaccination_status' => 'not_vaccinated',
                'breed' => 'Saanen',
                'purpose' => 'susu',
                'feed_type' => 'Rumput dan konsentrat',
                'daily_feed_kg' => 2.5,
                'housing_type' => 'Kandang semi terbuka',
                'milk_production_liter' => 2.5,
                'pregnancy_status' => 'hamil',
                'notes' => 'Kambing sedang sakit, perlu perawatan'
            ]
        ];

        foreach ($livestocks as $livestockData) {
            Livestock::updateOrCreate(
                ['identification_number' => $livestockData['identification_number']],
                $livestockData
            );
        }
    }
}
