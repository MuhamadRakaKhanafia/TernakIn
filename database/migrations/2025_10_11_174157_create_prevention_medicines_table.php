<?php
// 2024_01_01_000005_create_prevention_medicines_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('prevention_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->constrained()->onDelete('cascade');
            $table->enum('method_type', ['biosekuriti', 'vaksinasi', 'manajemen_pakan', 'sanitasi', 'lainnya']);
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->text('steps')->nullable();
            $table->enum('effectiveness', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->enum('cost_estimate', ['murah', 'sedang', 'mahal'])->default('sedang');
            $table->timestamps();
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->enum('type', ['antibiotik', 'vitamin', 'antiviral', 'antiparasit', 'vaksin', 'lainnya']);
            $table->text('description')->nullable();
            $table->text('dosage_guideline')->nullable();
            $table->text('administration_method')->nullable();
            $table->text('side_effects')->nullable();
            $table->string('price_range', 100)->nullable();
            $table->boolean('is_prescription_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('disease_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->constrained()->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->text('recommended_dosage')->nullable();
            $table->text('administration_notes')->nullable();
            $table->enum('effectiveness', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->boolean('is_preventive')->default(false);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('disease_medicines');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('prevention_methods');
    }
};