<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ebooks', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('cover')->nullable();
            $t->string('file')->nullable();
            $t->text('description')->nullable();
            $t->decimal('price', 10, 2)->default(0);
            $t->unsignedInteger('pages')->default(0);
            $t->boolean('is_free')->default(false);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('ebook_purchases', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('ebook_id')->constrained()->cascadeOnDelete();
            $t->foreignId('order_id')->nullable();
            $t->timestamps();
            $t->unique(['user_id','ebook_id']);
        });

        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('rating')->default(5);
            $t->text('comment')->nullable();
            $t->boolean('is_approved')->default(false);
            $t->timestamps();
        });

        Schema::create('certificates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->string('code')->unique();
            $t->timestamp('issued_at')->nullable();
            $t->timestamps();
        });

        Schema::create('announcements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->text('body');
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('live_classes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->string('meeting_url')->nullable();
            $t->string('platform')->default('zoom');
            $t->timestamp('starts_at')->nullable();
            $t->unsignedInteger('duration_minutes')->default(60);
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('live_classes');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('ebook_purchases');
        Schema::dropIfExists('ebooks');
    }
};
