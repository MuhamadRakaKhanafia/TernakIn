<?php
// 2024_01_01_000007_create_health_emergency_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('health_workers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('profession', ['dokter_hewan', 'paramedis', 'ahli_ternak', 'petugas_dinas']);
            $table->string('specialization', 200)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('city')->onDelete('set null');
            $table->boolean('is_verified')->default(false);
            $table->integer('years_of_experience')->nullable();
            $table->timestamps();
        });

        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name', 200);
            $table->enum('contact_type', ['dinas_peternakan', 'rumah_sakit_hewan', 'klinik', 'laboratorium', 'hotline']);
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 200)->nullable();
            $table->text('address')->nullable(); $table->foreignId('province_id')->nullable()->constrained('province')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained('city')->onDelete('set null');
            $table->text('operating_hours')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('health_workers');
    }
};