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
            $table->text('admin_notes')->nullable();
            $table->text('admin_recommendations')->nullable();
            $table->timestamp('admin_validated_at')->nullable();
            $table->foreignId('admin_validator_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->dropForeign(['admin_validator_id']);
            $table->dropColumn(['admin_notes', 'admin_recommendations', 'admin_validated_at', 'admin_validator_id']);
        });
    }
};
