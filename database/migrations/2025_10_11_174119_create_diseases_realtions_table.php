<?php
// 2024_01_01_000004_create_diseases_relations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->string('disease_code', 20)->unique();
            $table->string('name', 200);
            $table->text('other_names')->nullable();
            $table->enum('causative_agent', ['virus', 'bakteri', 'parasit', 'fungi', 'defisiensi_nutrisi', 'lainnya']);
            $table->text('description')->nullable();
            $table->enum('mortality_rate', ['rendah', 'sedang', 'tinggi', 'sangat_tinggi']);
            $table->boolean('is_zoonotic')->default(false);
            $table->text('transmission_method')->nullable();
            $table->text('diagnosis_method')->nullable();
            $table->text('general_treatment')->nullable();
            $table->text('emergency_actions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('disease_animal_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->constrained()->onDelete('cascade');
            $table->foreignId('animal_type_id')->constrained()->onDelete('cascade');
            $table->enum('severity', ['ringan', 'sedang', 'berat'])->default('sedang');
            $table->text('specific_notes')->nullable();
            $table->timestamps();
            $table->unique(['disease_id', 'animal_type_id']);
        });

        Schema::create('disease_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->constrained()->onDelete('cascade');
            $table->foreignId('symptom_id')->constrained()->onDelete('cascade');
            $table->decimal('probability', 3, 2)->default(0.50);
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['disease_id', 'symptom_id']);
        });
    }

    public function down() {
        Schema::dropIfExists('disease_symptoms');
        Schema::dropIfExists('disease_animal_types');
        Schema::dropIfExists('diseases');
    }
};