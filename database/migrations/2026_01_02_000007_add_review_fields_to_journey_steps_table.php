<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journey_steps', function (Blueprint $table) {
            if (!Schema::hasColumn('journey_steps', 'review_status')) {
                $table->string('review_status')->default('pending')->after('status');
            }
            if (!Schema::hasColumn('journey_steps', 'payload')) {
                $table->json('payload')->nullable()->after('review_status');
            }
            if (!Schema::hasColumn('journey_steps', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('payload');
            }
            if (!Schema::hasColumn('journey_steps', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('journey_steps', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('submitted_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('journey_steps', function (Blueprint $table) {
            $cols = array_filter(['review_status', 'payload', 'admin_notes', 'submitted_at', 'approved_at'], fn($c) => Schema::hasColumn('journey_steps', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
