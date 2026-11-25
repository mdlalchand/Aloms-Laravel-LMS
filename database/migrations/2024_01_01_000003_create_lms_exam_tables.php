<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exams', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->text('description')->nullable();
            $t->unsignedInteger('duration_minutes')->default(30);
            $t->decimal('total_marks', 8, 2)->default(0);
            $t->decimal('pass_marks', 8, 2)->default(0);
            $t->decimal('negative_marking', 4, 2)->default(0);
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('ends_at')->nullable();
            $t->unsignedInteger('attempt_limit')->default(0);
            $t->boolean('shuffle_questions')->default(false);
            $t->boolean('is_free')->default(false);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('questions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $t->string('type')->default('mcq'); // mcq|cq|short
            $t->longText('question');
            $t->text('explanation')->nullable();
            $t->decimal('marks', 6, 2)->default(1);
            $t->unsignedInteger('position')->default(0);
            $t->timestamps();
        });

        Schema::create('options', function (Blueprint $t) {
            $t->id();
            $t->foreignId('question_id')->constrained()->cascadeOnDelete();
            $t->string('option_text');
            $t->boolean('is_correct')->default(false);
            $t->unsignedInteger('position')->default(0);
            $t->timestamps();
        });

        Schema::create('exam_attempts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->decimal('score', 8, 2)->default(0);
            $t->decimal('correct_count', 8, 2)->default(0);
            $t->decimal('wrong_count', 8, 2)->default(0);
            $t->string('status')->default('in_progress'); // in_progress|submitted|expired
            $t->timestamps();
        });

        Schema::create('attempt_answers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete();
            $t->foreignId('question_id')->constrained()->cascadeOnDelete();
            $t->foreignId('option_id')->nullable()->constrained()->nullOnDelete();
            $t->text('answer_text')->nullable();
            $t->boolean('is_correct')->default(false);
            $t->decimal('marks_awarded', 6, 2)->default(0);
            $t->timestamps();
            $t->unique(['exam_attempt_id','question_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exams');
    }
};
