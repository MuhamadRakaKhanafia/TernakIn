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
        // Hanya tambah kolom yang benar-benar belum ada
        $columnsToAdd = [];
        
        if (!Schema::hasColumn('users', 'phone')) {
            $columnsToAdd[] = 'phone';
        }
        if (!Schema::hasColumn('users', 'user_type')) {
            $columnsToAdd[] = 'user_type';
        }
        if (!Schema::hasColumn('users', 'location_id')) {
            $columnsToAdd[] = 'location_id';
        }
        if (!Schema::hasColumn('users', 'last_login')) {
            $columnsToAdd[] = 'last_login';
        }
        if (!Schema::hasColumn('users', 'is_active')) {
            $columnsToAdd[] = 'is_active';
        }
        // Provider dan provider_id sudah ada, jadi skip
        // if (!Schema::hasColumn('users', 'provider')) {
        //     $columnsToAdd[] = 'provider';
        // }
        // if (!Schema::hasColumn('users', 'provider_id')) {
        //     $columnsToAdd[] = 'provider_id';
        // }
        if (!Schema::hasColumn('users', 'avatar')) {
            $columnsToAdd[] = 'avatar';
        }

        // Jika ada kolom yang perlu ditambah, jalankan alter table
        if (!empty($columnsToAdd)) {
            Schema::table('users', function (Blueprint $table) use ($columnsToAdd) {
                foreach ($columnsToAdd as $column) {
                    switch ($column) {
                        case 'phone':
                            $table->string('phone', 20)->nullable()->after('email');
                            break;
                        case 'user_type':
                            $table->enum('user_type', ['peternak', 'dokter', 'admin'])->default('peternak')->after('phone');
                            break;
                        case 'location_id':
                            $table->unsignedBigInteger('location_id')->nullable()->after('user_type');
                            break;
                        case 'last_login':
                            $table->timestamp('last_login')->nullable()->after('location_id');
                            break;
                        case 'is_active':
                            $table->boolean('is_active')->default(true)->after('last_login');
                            break;
                        case 'avatar':
                            $table->string('avatar')->nullable()->after('provider_id');
                            break;
                    }
                }
            });
        }

        // Tambahkan foreign key jika location_id ada
        if (Schema::hasColumn('users', 'location_id') && Schema::hasTable('user_locations')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('location_id')
                      ->references('id')
                      ->on('user_locations')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key
            if (Schema::hasColumn('users', 'location_id')) {
                $table->dropForeign(['location_id']);
            }
            
            // Hapus kolom yang mungkin ada
            $possibleColumns = ['phone', 'user_type', 'location_id', 'last_login', 'is_active', 'avatar'];
            $columnsToDrop = array_filter($possibleColumns, function ($column) {
                return Schema::hasColumn('users', $column);
            });
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};