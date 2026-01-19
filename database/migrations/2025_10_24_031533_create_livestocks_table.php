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
            
            // Foreign key
            $table->foreignId('animal_type_id')->constrained()->onDelete('cascade');
            
            // General information
            $table->string('name');
            $table->string('identification_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('acquisition_date')->nullable();
            
            // Chicken specific fields
            $table->string('strain')->nullable();
            $table->integer('age_weeks')->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->string('feed_type')->nullable();
            $table->decimal('daily_feed_kg', 8, 2)->nullable();
            $table->integer('egg_production')->nullable();
            $table->string('health_status')->nullable();
            $table->string('vaccination_status')->nullable();
            $table->string('housing_type')->nullable();
            $table->integer('flock_size')->nullable();
            
            // Large animal specific fields
            $table->string('breed')->nullable();
            $table->integer('age_months')->nullable();
            $table->string('purpose')->nullable();
            $table->decimal('milk_production_liter', 8, 2)->nullable();
            $table->string('pregnancy_status')->nullable();
            
            // Common fields
            $table->enum('sex', ['jantan', 'betina'])->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
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