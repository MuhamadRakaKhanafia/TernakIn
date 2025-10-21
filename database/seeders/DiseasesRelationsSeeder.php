<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiseaseRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diseaseAnimalTypes = [
            // FMD - Sapi, Kambing, Domba, Babi
            ['disease_id' => 1, 'animal_type_id' => 1, 'severity' => 'berat', 'specific_notes' => 'Mortalitas tinggi pada anak sapi'],
            ['disease_id' => 1, 'animal_type_id' => 2, 'severity' => 'sedang', 'specific_notes' => 'Gejala lebih ringan dibanding sapi'],
            ['disease_id' => 1, 'animal_type_id' => 3, 'severity' => 'sedang', 'specific_notes' => 'Gejala lebih ringan dibanding sapi'],
            ['disease_id' => 1, 'animal_type_id' => 6, 'severity' => 'berat', 'specific_notes' => 'Sangat rentan terhadap FMD'],
            
            // Avian Influenza - Ayam, Bebek
            ['disease_id' => 2, 'animal_type_id' => 4, 'severity' => 'sangat_tinggi', 'specific_notes' => 'Mortalitas bisa mencapai 100%'],
            ['disease_id' => 2, 'animal_type_id' => 5, 'severity' => 'tinggi', 'specific_notes' => 'Bebek sering sebagai carrier tanpa gejala'],
            
            // Brucellosis - Sapi, Kambing
            ['disease_id' => 3, 'animal_type_id' => 1, 'severity' => 'sedang', 'specific_notes' => 'Menyebabkan abortus pada trimester akhir'],
            ['disease_id' => 3, 'animal_type_id' => 2, 'severity' => 'ringan', 'specific_notes' => 'Gejala lebih ringan'],
            
            // Newcastle Disease - Ayam
            ['disease_id' => 4, 'animal_type_id' => 4, 'severity' => 'sangat_tinggi', 'specific_notes' => 'Strain velogenik mematikan'],
            
            // Mastitis - Sapi
            ['disease_id' => 5, 'animal_type_id' => 1, 'severity' => 'sedang', 'specific_notes' => 'Biasanya terjadi pasca melahirkan'],
            
            // Cacingan - Semua ternak
            ['disease_id' => 6, 'animal_type_id' => 1, 'severity' => 'ringan', 'specific_notes' => 'Mempengaruhi pertumbuhan pedet'],
            ['disease_id' => 6, 'animal_type_id' => 2, 'severity' => 'sedang', 'specific_notes' => 'Cacing hati pada kambing'],
            ['disease_id' => 6, 'animal_type_id' => 3, 'severity' => 'sedang', 'specific_notes' => 'Cacing paru pada domba'],
            ['disease_id' => 6, 'animal_type_id' => 4, 'severity' => 'ringan', 'specific_notes' => 'Cacing gelang pada ayam'],
            ['disease_id' => 6, 'animal_type_id' => 5, 'severity' => 'ringan', 'specific_notes' => 'Cacing pada saluran pencernaan'],
            ['disease_id' => 6, 'animal_type_id' => 6, 'severity' => 'sedang', 'specific_notes' => 'Cacing paru pada babi'],
            ['disease_id' => 6, 'animal_type_id' => 7, 'severity' => 'ringan', 'specific_notes' => 'Cacing pada usus kelinci'],
            ['disease_id' => 6, 'animal_type_id' => 8, 'severity' => 'ringan', 'specific_notes' => 'Parasit internal pada ikan'],
            
            // Scabies - Kambing, Domba
            ['disease_id' => 7, 'animal_type_id' => 2, 'severity' => 'sedang', 'specific_notes' => 'Tungau Sarcoptes scabiei'],
            ['disease_id' => 7, 'animal_type_id' => 3, 'severity' => 'sedang', 'specific_notes' => 'Menyebabkan kerusakan kulit dan bulu'],
            
            // Defisiensi Kalsium - Sapi
            ['disease_id' => 8, 'animal_type_id' => 1, 'severity' => 'berat', 'specific_notes' => 'Biasanya terjadi 24-48 jam pasca melahirkan'],
        ];

        DB::table('disease_animal_types')->insert($diseaseAnimalTypes);
        
        $this->command->info('✅ Disease-Animal Type relations seeded successfully! Total: ' . count($diseaseAnimalTypes) . ' relations');
    }
}