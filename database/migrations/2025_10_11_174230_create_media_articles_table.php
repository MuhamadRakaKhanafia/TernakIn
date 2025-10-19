<?php
// 2024_01_01_000006_create_media_articles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('disease_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->constrained()->onDelete('cascade');
            $table->string('image_url', 255);
            $table->string('caption', 300)->nullable();
            $table->enum('image_type', ['gejala', 'penyebab', 'pencegahan', 'pengobatan']);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('disease_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->constrained()->onDelete('cascade');
            $table->string('video_url', 255);
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
            $table->integer('duration')->nullable();
            $table->string('thumbnail_url', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 300);
            $table->string('slug', 350)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('featured_image', 255)->nullable();
            $table->enum('article_type', ['tips', 'berita', 'edukasi', 'teknologi']);
            $table->integer('reading_time')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('article_animal_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('animal_type_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('article_animal_types');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('disease_videos');
        Schema::dropIfExists('disease_images');
    }
};