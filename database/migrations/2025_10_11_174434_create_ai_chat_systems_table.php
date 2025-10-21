<?php
// 2024_01_01_000009_create_ai_chat_system_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up()
{
    Schema::create('ai_chat_sessions', function (Blueprint $table) {
        $table->id();
        $table->uuid('session_id')->unique();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('animal_type_id')->nullable()->constrained()->onDelete('set null');
        $table->string('title');
        $table->timestamp('last_activity');
        $table->timestamps();
        
        $table->index(['user_id', 'last_activity']);
        $table->index('session_id');
    });

    Schema::create('ai_chat_messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('session_id')->constrained('ai_chat_sessions')->onDelete('cascade');
        $table->enum('role', ['user', 'assistant']);
        $table->text('content');
        $table->timestamps();
        
        $table->index(['session_id', 'created_at']);
        $table->index('role');
    });

    Schema::create('ai_usage_analytics', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('session_id')->constrained('ai_chat_sessions')->onDelete('cascade');
        $table->integer('input_tokens')->default(0);
        $table->integer('output_tokens')->default(0);
        $table->integer('total_tokens')->default(0);
        $table->decimal('cost', 10, 6)->default(0);
        $table->timestamps();
        
        $table->index(['user_id', 'created_at']);
        $table->index('session_id');
    });
}

    public function down() {
        Schema::dropIfExists('ai_usage_analytics');
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_sessions');
    }
};