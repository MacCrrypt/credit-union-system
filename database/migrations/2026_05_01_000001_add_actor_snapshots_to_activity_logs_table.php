<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('user_name')->nullable()->after('user_id');
            $table->string('user_email')->nullable()->after('user_name');
            $table->string('user_role')->nullable()->after('user_email');
            $table->foreignId('user_branch_id')->nullable()->after('user_role');
            $table->string('user_branch_name')->nullable()->after('user_branch_id');
            $table->foreignId('user_created_by')->nullable()->after('user_branch_name');
        });

        $logs = DB::table('activity_logs')
            ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
            ->leftJoin('branches', 'branches.id', '=', 'users.branch_id')
            ->select(
                'activity_logs.id',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role',
                'users.branch_id as user_branch_id',
                'branches.name as user_branch_name',
                'users.created_by as user_created_by'
            )
            ->get();

        foreach ($logs as $log) {
            DB::table('activity_logs')
                ->where('id', $log->id)
                ->update([
                    'user_name' => $log->user_name,
                    'user_email' => $log->user_email,
                    'user_role' => $log->user_role,
                    'user_branch_id' => $log->user_branch_id,
                    'user_branch_name' => $log->user_branch_name,
                    'user_created_by' => $log->user_created_by,
                ]);
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['user_role', 'user_branch_id', 'user_created_by'], 'activity_actor_scope_index');
            $table->index(['user_branch_id', 'created_at'], 'activity_branch_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_actor_scope_index');
            $table->dropIndex('activity_branch_created_index');
            $table->dropColumn([
                'user_name',
                'user_email',
                'user_role',
                'user_branch_id',
                'user_branch_name',
                'user_created_by',
            ]);
        });
    }
};
