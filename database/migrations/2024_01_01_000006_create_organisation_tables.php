<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Branches (physical / online centres)
        Schema::create('branches', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('code')->unique();
            $t->string('address')->nullable();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('branch_id')->nullable()->after('role')->constrained('branches')->nullOnDelete();
        });

        // Guardian <-> student link
        Schema::create('guardian_students', function (Blueprint $t) {
            $t->id();
            $t->foreignId('guardian_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $t->string('relation')->default('guardian'); // father|mother|guardian
            $t->timestamps();
            $t->unique(['guardian_id', 'student_id']);
        });

        // Home page hero sliders
        Schema::create('sliders', function (Blueprint $t) {
            $t->id();
            $t->string('badge')->nullable();
            $t->string('title');
            $t->string('subtitle')->nullable();
            $t->string('image')->nullable();
            $t->string('button_text')->nullable();
            $t->string('button_url')->nullable();
            $t->string('theme')->default('emerald'); // emerald|indigo|rose|amber|slate
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('guardian_students');
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('branch_id');
        });
        Schema::dropIfExists('branches');
    }
};
