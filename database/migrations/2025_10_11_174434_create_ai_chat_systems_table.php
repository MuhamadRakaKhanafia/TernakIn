<?php
// 2024_01_01_000009_create_ai_chat_system_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('ai_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('animal_type_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title', 200)->nullable();
            $table->integer('message_count')->default(0);
            $table->integer('token_count')->default(0);
            $table->timestamp('last_activity')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ai_chat_sessions')->onDelete('cascade');
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->integer('token_count')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->string('model_used', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('ai_usage_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('usage_date');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('total_requests')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('total_cost', 10, 6)->default(0);
            $table->string('model_used', 50);
            $table->timestamps();
            $table->unique(['usage_date', 'user_id', 'model_used']);
        });
    }

    public function down() {
        Schema::dropIfExists('ai_usage_analytics');
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_sessions');
    }
};