<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Check if table exists, if not create it
        if (!Schema::hasTable('animal_types')) {
            Schema::create('animal_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->enum('category', ['poultry', 'large_animal', 'other'])->default('other');
                $table->timestamps();
            });

            // Insert default animal types
            DB::table('animal_types')->insert([
                [
                    'name' => 'Ayam',
                    'description' => 'Unggas domestik untuk produksi telur dan daging',
                    'category' => 'poultry',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Sapi',
                    'description' => 'Ternak besar untuk produksi daging dan susu',
                    'category' => 'large_animal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Kambing',
                    'description' => 'Ternak kecil untuk produksi daging dan susu',
                    'category' => 'large_animal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Domba',
                    'description' => 'Ternak kecil untuk produksi daging dan wol',
                    'category' => 'large_animal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Bebek',
                    'description' => 'Unggas air untuk produksi telur dan daging',
                    'category' => 'poultry',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Kelinci',
                    'description' => 'Hewan ternak kecil untuk produksi daging',
                    'category' => 'other',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('animal_types');
    }
};