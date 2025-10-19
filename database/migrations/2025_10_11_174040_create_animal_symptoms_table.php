<?php
// 2024_01_01_000003_create_animal_symptoms_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('animal_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('scientific_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('icon_url', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('symptoms', function (Blueprint $table) {
            $table->id();
            $table->string('symptom_code', 20)->unique();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->enum('severity_level', ['ringan', 'sedang', 'berat'])->default('sedang');
            $table->boolean('is_common')->default(false);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('symptoms');
        Schema::dropIfExists('animal_types');
    }
};