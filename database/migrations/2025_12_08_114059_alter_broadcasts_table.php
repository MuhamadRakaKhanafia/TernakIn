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
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropColumn(['title', 'type', 'priority']);
            $table->string('link_url')->nullable()->after('message');
            $table->string('link_text')->nullable()->after('link_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropColumn(['link_url', 'link_text']);
            $table->string('title');
            $table->enum('type', ['disease_warning', 'promo_feed_vaccine', 'system_update']);
            $table->integer('priority')->default(0);
        });
    }
};
