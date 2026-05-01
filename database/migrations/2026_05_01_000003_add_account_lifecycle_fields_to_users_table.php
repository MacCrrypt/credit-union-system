<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active')->after('role');
            $table->boolean('must_change_password')->default(false)->after('password');
            $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            $table->timestamp('last_login_at')->nullable()->after('password_changed_at');
            $table->timestamp('status_changed_at')->nullable()->after('last_login_at');
            $table->foreignId('status_changed_by')->nullable()->after('status_changed_at')->constrained('users')->nullOnDelete();
            $table->text('status_reason')->nullable()->after('status_changed_by');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['status', 'role', 'branch_id']);
            $table->index(['must_change_password', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status', 'role', 'branch_id']);
            $table->dropIndex(['must_change_password', 'status']);
            $table->dropForeign(['status_changed_by']);
            $table->dropColumn([
                'status',
                'must_change_password',
                'password_changed_at',
                'last_login_at',
                'status_changed_at',
                'status_changed_by',
                'status_reason',
            ]);
        });
    }
};
