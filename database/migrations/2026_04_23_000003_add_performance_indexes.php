<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // These indexes target the exact filters used by user lists, member searches,
        // exports, and activity-log views as the system grows.
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'branch_id']);
            $table->index(['created_by', 'role']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->index(['created_by', 'created_at']);
            $table->index(['name', 'created_at']);
        });

        Schema::table('signatures', function (Blueprint $table) {
            $table->index(['created_by', 'member_id']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'branch_id']);
            $table->dropIndex(['created_by', 'role']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['created_by', 'created_at']);
            $table->dropIndex(['name', 'created_at']);
        });

        Schema::table('signatures', function (Blueprint $table) {
            $table->dropIndex(['created_by', 'member_id']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['action', 'created_at']);
        });
    }
};
