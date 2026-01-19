<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiseasesSeeder extends Seeder
{
    public function run(): void
    {
        $diseases = [
            [
                'name' => 'Foot and Mouth Disease (FMD)',
                'disease_code' => 'FMD-001',
                'causative_agent' => 'Virus Aphthovirus',
                'description' => 'Penyakit mulut dan kuku adalah penyakit menular yang sangat menular yang mempengaruhi ruminansia dan babi. Ditandai dengan demam tinggi dan lepuh pada mulut dan kuku.',
                'transmission_method' => 'Melalui kontak langsung dengan hewan yang terinfeksi, air, pakan, atau objek yang terkontaminasi.',
                'general_treatment' => 'Tidak ada pengobatan spesifik. Vaksinasi preventif dan isolasi hewan yang terinfeksi. Perawatan suportif untuk mencegah infeksi sekunder.',
                'prevention_method' => 'Vaksinasi rutin, biosekuriti ketat, karantina hewan baru, desinfeksi kandang dan peralatan.',
                'risk_factors' => 'Kepadatan ternak tinggi, transportasi hewan, kontaminasi pakan dan air.',
                'is_zoonotic' => 1,
                'is_active' => 1,
                'image' => 'diseases/fmd.jpg',
            ],
            [
                'name' => 'Avian Influenza (Flu Burung)',
                'disease_code' => 'AI-002',
                'causative_agent' => 'Virus Influenza A',
                'description' => 'Penyakit pernapasan akut pada unggas yang disebabkan oleh virus influenza. Dapat menyebabkan kematian tinggi dalam waktu singkat.',
                'transmission_method' => 'Melalui kontak dengan unggas yang terinfeksi, feses, atau lingkungan yang terkontaminasi. Juga melalui udara.',
                'general_treatment' => 'Tidak ada pengobatan spesifik. Vaksinasi dan biosecurity. Isolasi dan depopulasi pada kasus berat.',
                'prevention_method' => 'Vaksinasi, biosekuriti ketat, monitoring rutin, kontrol lalu lintas unggas.',
                'risk_factors' => 'Peternakan dengan multi-usia, kontak dengan unggas liar, sanitasi buruk.',
                'is_zoonotic' => 1,
                'is_active' => 1,
                'image' => 'diseases/avian-influenza.jpg',
            ],
            [
                'name' => 'Brucellosis',
                'disease_code' => 'BRU-003',
                'causative_agent' => 'Bakteri Brucella spp',
                'description' => 'Penyakit bakteri yang mempengaruhi reproduksi hewan dan dapat menular ke manusia. Menyebabkan abortus dan infertilitas.',
                'transmission_method' => 'Melalui kontak dengan cairan reproduksi, susu, atau jaringan hewan yang terinfeksi.',
                'general_treatment' => 'Antibiotik untuk manusia; vaksinasi untuk hewan. Pengobatan jangka panjang diperlukan.',
                'prevention_method' => 'Vaksinasi, testing rutin, isolasi hewan positif, pasteurisasi susu.',
                'risk_factors' => 'Kawin alam, persalinan tanpa isolasi, konsumsi produk susu mentah.',
                'is_zoonotic' => 1,
                'is_active' => 1,
                'image' => 'diseases/brucellosis.jpg',
            ],
            [
                'name' => 'Newcastle Disease',
                'disease_code' => 'ND-004',
                'causative_agent' => 'Virus Avian Paramyxovirus',
                'description' => 'Penyakit pernapasan dan saraf pada unggas yang sangat menular. Dapat menyebabkan kematian hingga 100% pada ternak rentan.',
                'transmission_method' => 'Melalui kontak langsung, udara, atau objek yang terkontaminasi. Juga melalui pakan dan air.',
                'general_treatment' => 'Vaksinasi preventif. Tidak ada pengobatan spesifik untuk kasus klinis.',
                'prevention_method' => 'Vaksinasi rutin, biosekuriti, kontrol lalu lintas unggas, sanitasi kandang.',
                'risk_factors' => 'Kepadatan tinggi, ventilasi buruk, multi-usia dalam satu kandang.',
                'is_zoonotic' => 0,
                'is_active' => 1,
                'image' => 'diseases/newcastle.jpg',
            ],
            [
                'name' => 'Mastitis',
                'disease_code' => 'MAS-005',
                'causative_agent' => 'Bakteri Staphylococcus, Streptococcus',
                'description' => 'Peradangan pada ambing sapi yang menyebabkan penurunan produksi susu dan kualitas susu. Dapat bersifat klinis atau subklinis.',
                'transmission_method' => 'Melalui luka pada putting atau kondisi sanitasi yang buruk selama pemerahan.',
                'general_treatment' => 'Antibiotik intramammary, perawatan suportif, kompres hangat, anti-inflamasi.',
                'prevention_method' => 'Kebersihan pemerahan, dipping putting, perawatan putting, manajemen kandang bersih.',
                'risk_factors' => 'Mesin perah tidak steril, kebersihan pekerja, luka pada putting, kandang basah.',
                'is_zoonotic' => 0,
                'is_active' => 1,
                'image' => 'diseases/mastitis.jpg',
            ],
        ];

        // Insert data
        foreach ($diseases as $disease) {
            DB::table('diseases')->insertOrIgnore($disease);
        }
    }
}