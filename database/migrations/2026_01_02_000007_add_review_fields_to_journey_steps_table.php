<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journey_steps', function (Blueprint $table) {
            $table->string('review_status')->default('pending')->after('status');
            $table->json('payload')->nullable()->after('review_status');
            $table->text('admin_notes')->nullable()->after('payload');
            $table->timestamp('submitted_at')->nullable()->after('admin_notes');
            $table->timestamp('approved_at')->nullable()->after('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('journey_steps', function (Blueprint $table) {
            $table->dropColumn([
                'review_status',
                'payload',
                'admin_notes',
                'submitted_at',
                'approved_at',
            ]);
        });
    }
};
