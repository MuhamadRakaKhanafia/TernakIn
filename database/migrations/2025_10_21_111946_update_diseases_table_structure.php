<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek jika kolom belum ada, lalu tambahkan
        if (!Schema::hasColumn('diseases', 'disease_code')) {
            Schema::table('diseases', function (Blueprint $table) {
                $table->string('disease_code')->unique()->after('id');
            });
        }

        if (!Schema::hasColumn('diseases', 'causative_agent')) {
            Schema::table('diseases', function (Blueprint $table) {
                $table->string('causative_agent')->after('disease_code');
            });
        }

        if (!Schema::hasColumn('diseases', 'transmission_method')) {
            Schema::table('diseases', function (Blueprint $table) {
                $table->text('transmission_method')->after('is_zoonotic');
            });
        }

        if (!Schema::hasColumn('diseases', 'general_treatment')) {
            Schema::table('diseases', function (Blueprint $table) {
                $table->text('general_treatment')->after('transmission_method');
            });
        }

        if (!Schema::hasColumn('diseases', 'is_active')) {
            Schema::table('diseases', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('general_treatment');
            });
        }
    }

    public function down(): void
    {
        
    }
};