<?php
// 2024_01_01_000008_create_questions_interactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('animal_type_id')->constrained()->onDelete('cascade');
            $table->string('title', 300);
            $table->text('question_text');
            $table->text('symptoms_description')->nullable();
            $table->json('images_urls')->nullable();
            $table->enum('status', ['menunggu', 'dijawab', 'ditutup'])->default('menunggu');
            $table->timestamp('asked_at')->useCurrent();
            $table->timestamp('answered_at')->nullable();
            $table->text('answer_text')->nullable();
            $table->foreignId('answered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('search_query', 300);
            $table->foreignId('animal_type_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('search_results_count')->default(0);
            $table->timestamp('searched_at')->useCurrent();
            $table->string('user_ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('article_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('viewed_at')->useCurrent();
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('article_views');
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('questions');
    }
};