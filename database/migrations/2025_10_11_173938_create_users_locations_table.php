<?php
// 2024_01_01_000002_create_users_locations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        if (!Schema::hasTable('user_locations')) {
            Schema::create('user_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('province')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('city')->onDelete('cascade');
            $table->string('district')->nullable();
            $table->string('village')->nullable();
            $table->text('detailed_address')->nullable();
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50)->unique();
                $table->string('email', 100)->unique();
                $table->string('password');
                $table->string('full_name', 100)->nullable();
                $table->string('phone', 20)->nullable();
                $table->enum('user_type', ['peternak','admin'])->default('peternak');
                $table->foreignId('location_id')->nullable()->constrained('user_locations')->onDelete('set null');
                $table->timestamp('last_login')->nullable();
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    public function down() {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_locations');
    }
};