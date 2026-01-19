<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SymptomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $symptoms = [
            ['symptom_code' => 'FEV', 'name' => 'Demam Tinggi'],
            ['symptom_code' => 'COU', 'name' => 'Batuk'],
            ['symptom_code' => 'DIF', 'name' => 'Kesulitan Bernapas'],
            ['symptom_code' => 'LES', 'name' => 'Lesu'],
            ['symptom_code' => 'APP', 'name' => 'Hilang Nafsu Makan'],
            ['symptom_code' => 'DIA', 'name' => 'Diare'],
            ['symptom_code' => 'VOM', 'name' => 'Muntah'],
            ['symptom_code' => 'WEI', 'name' => 'Penurunan Berat Badan'],
            ['symptom_code' => 'SWO', 'name' => 'Pembengkakan'],
            ['symptom_code' => 'LIM', 'name' => 'Kelemahan Otot'],
            ['symptom_code' => 'SKI', 'name' => 'Perubahan Kulit'],
            ['symptom_code' => 'HAI', 'name' => 'Kerontokan Bulu'],
            ['symptom_code' => 'EYE', 'name' => 'Masalah Mata'],
            ['symptom_code' => 'NAS', 'name' => 'Keluar Cairan Hidung'],
            ['symptom_code' => 'SAL', 'name' => 'Bersendawa'],
            ['symptom_code' => 'CON', 'name' => 'Konstipasi'],
            ['symptom_code' => 'LAM', 'name' => 'Pincang'],
            ['symptom_code' => 'PAR', 'name' => 'Kelumpuhan'],
            ['symptom_code' => 'SEP', 'name' => 'Sepsis'],
            ['symptom_code' => 'ANE', 'name' => 'Anemia'],
            ['symptom_code' => 'ICT', 'name' => 'Ikterus'],
            ['symptom_code' => 'ABN', 'name' => 'Perilaku Abnormal'],
            ['symptom_code' => 'AGR', 'name' => 'Agresi'],
            ['symptom_code' => 'DEP', 'name' => 'Depresi'],
            ['symptom_code' => 'COM', 'name' => 'Koma'],
            ['symptom_code' => 'CONV', 'name' => 'Kejang'],
            ['symptom_code' => 'STR', 'name' => 'Sterilitas'],
            ['symptom_code' => 'ABO', 'name' => 'Aborsi'],
            ['symptom_code' => 'MIL', 'name' => 'Produksi Susu Menurun'],
            ['symptom_code' => 'EGG', 'name' => 'Produksi Telur Menurun'],
        ];

        foreach ($symptoms as $symptom) {
            DB::table('symptoms')->insert([
                'symptom_code' => $symptom['symptom_code'],
                'name' => $symptom['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
