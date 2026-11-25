<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('icon')->nullable();
            $t->string('image')->nullable();
            $t->text('description')->nullable();
            $t->unsignedInteger('position')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('courses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('subtitle')->nullable();
            $t->longText('description')->nullable();
            $t->text('requirements')->nullable();
            $t->json('outcomes')->nullable();
            $t->string('thumbnail')->nullable();
            $t->string('promo_video')->nullable();
            $t->decimal('price', 10, 2)->default(0);
            $t->decimal('discount_price', 10, 2)->nullable();
            $t->string('level')->default('beginner'); // beginner|intermediate|advanced
            $t->string('language')->default('Bangla');
            $t->unsignedInteger('duration_minutes')->default(0);
            $t->boolean('is_published')->default(false);
            $t->boolean('is_featured')->default(false);
            $t->boolean('has_certificate')->default(true);
            $t->timestamp('starts_at')->nullable();
            $t->unsignedInteger('seat_limit')->nullable();
            $t->string('meta_title')->nullable();
            $t->text('meta_description')->nullable();
            $t->timestamps();
        });

        Schema::create('sections', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->unsignedInteger('position')->default(0);
            $t->timestamps();
        });

        Schema::create('lessons', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('section_id')->nullable()->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->string('type')->default('video'); // video|text|pdf|live|exam
            $t->string('video_url')->nullable();
            $t->string('attachment')->nullable();
            $t->longText('content')->nullable();
            $t->unsignedInteger('duration_minutes')->default(0);
            $t->unsignedInteger('position')->default(0);
            $t->boolean('is_free_preview')->default(false);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('order_id')->nullable();
            $t->decimal('price_paid', 10, 2)->default(0);
            $t->string('status')->default('active'); // active|expired|cancelled
            $t->timestamp('enrolled_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id','course_id']);
        });

        Schema::create('lesson_progress', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('watched_seconds')->default(0);
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id','lesson_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('categories');
    }
};
