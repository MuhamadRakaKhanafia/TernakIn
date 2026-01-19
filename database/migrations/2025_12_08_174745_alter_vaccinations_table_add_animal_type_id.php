<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->foreignId('animal_type_id')->constrained()->onDelete('cascade');
            $table->dropForeign(['livestock_id']);
            $table->dropColumn('livestock_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->foreignId('livestock_id')->constrained()->onDelete('cascade');
            $table->dropForeign(['animal_type_id']);
            $table->dropColumn('animal_type_id');
        });
    }
};
