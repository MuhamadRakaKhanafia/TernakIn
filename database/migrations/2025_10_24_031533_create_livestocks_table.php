<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('livestocks', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('animal_type_id')->nullable()->constrained()->onDelete('set null');
            
            // General information
            $table->string('name');
            $table->string('identification_number')->nullable()->unique();
            $table->enum('sex', ['jantan', 'betina']);
            $table->date('birth_date')->nullable();
            $table->date('acquisition_date');
            
            // Age fields based on category
            $table->integer('age_weeks')->nullable(); // for poultry
            $table->integer('age_months')->nullable(); // for large animals
            
            // Physical attributes
            $table->decimal('weight_kg', 8, 2)->nullable();
            
            // Health status
            $table->enum('health_status', ['sehat', 'sakit']);
            $table->enum('vaccination_status', ['up_to_date', 'need_update', 'not_vaccinated']);
            
            // Feeding information
            $table->string('feed_type')->nullable();
            $table->decimal('daily_feed_kg', 8, 2)->nullable();
            
            // Housing information
            $table->string('housing_type')->nullable();
            $table->string('housing_size')->nullable();
            
            // Chicken specific fields
            $table->string('strain')->nullable();
            $table->integer('egg_production')->nullable();
            $table->integer('flock_size')->nullable();
            
            // Large animal specific fields
            $table->string('breed')->nullable();
            $table->string('purpose')->nullable();
            $table->decimal('milk_production_liter', 8, 2)->nullable();
            $table->enum('pregnancy_status', ['tidak_hamil', 'hamil'])->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('animal_type_id');
            $table->index('health_status');
            $table->index('vaccination_status');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('livestocks');
    }
};