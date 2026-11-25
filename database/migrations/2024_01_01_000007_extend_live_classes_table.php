<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('live_classes', function (Blueprint $t) {
            $t->text('description')->nullable()->after('title');
            $t->string('meeting_password')->nullable()->after('meeting_url');
            $t->string('recording_url')->nullable()->after('meeting_password');
            $t->boolean('is_published')->default(true)->after('duration_minutes');
        });
    }

    public function down(): void {
        Schema::table('live_classes', function (Blueprint $t) {
            $t->dropColumn(['description', 'meeting_password', 'recording_url', 'is_published']);
        });
    }
};
