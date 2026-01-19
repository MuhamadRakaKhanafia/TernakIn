<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add missing columns to diseases table
        if (Schema::hasTable('diseases')) {
            Schema::table('diseases', function (Blueprint $table) {
                if (!Schema::hasColumn('diseases', 'prevention_method')) {
                    $table->text('prevention_method')->nullable()->after('general_treatment');
                }
                
                // Add other missing columns if needed
                if (!Schema::hasColumn('diseases', 'risk_factors')) {
                    $table->text('risk_factors')->nullable()->after('prevention_method');
                }
                
                if (!Schema::hasColumn('diseases', 'is_zoonotic')) {
                    $table->boolean('is_zoonotic')->default(false)->after('risk_factors');
                }
                
                if (!Schema::hasColumn('diseases', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('is_zoonotic');
                }

                if (!Schema::hasColumn('diseases', 'image')) {
                    $table->string('image')->nullable()->after('is_active');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('diseases', function (Blueprint $table) {
            $table->dropColumn(['prevention_method', 'risk_factors', 'is_zoonotic', 'is_active', 'image']);
        });
    }
};