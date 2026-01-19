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
        Schema::table('livestocks', function (Blueprint $table) {
            if (!Schema::hasColumn('livestocks', 'housing_size')) {
                $table->string('housing_size')->nullable()->after('housing_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestocks', function (Blueprint $table) {
            $table->dropColumn('housing_size');
        });
    }
};
