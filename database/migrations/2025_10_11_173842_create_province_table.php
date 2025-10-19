<?php
// 2024_01_01_000001_create_provinces_table.php
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
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->enum('type', ['kabupaten', 'kota']);
            $table->string('code', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('City');
        Schema::dropIfExists('province');
    }
};