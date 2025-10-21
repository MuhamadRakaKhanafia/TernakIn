<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('province', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('city', function (Blueprint $table) {
            $table->id();
            // PERBAIKI: Reference ke 'province' (tanpa 's')
            $table->foreignId('province_id')->constrained('province')->onDelete('cascade');
            $table->string('name', 100);
            $table->enum('type', ['kabupaten', 'kota']);
            $table->string('code', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('city');
        Schema::dropIfExists('province');
    }
};