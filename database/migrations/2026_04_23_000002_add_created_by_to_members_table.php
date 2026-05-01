<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Member ownership powers staff counts, branch reporting, and audit visibility.
            $table->foreignId('created_by')->nullable()->after('phone')->constrained('users')->nullOnDelete();
        });

        // Backfill ownership from the signature uploader so existing records gain
        // a best-effort creator value without manual data cleanup.
        $members = DB::table('members')
            ->leftJoin('signatures', 'signatures.member_id', '=', 'members.id')
            ->select('members.id', 'signatures.created_by')
            ->get();

        foreach ($members as $member) {
            DB::table('members')
                ->where('id', $member->id)
                ->update(['created_by' => $member->created_by]);
        }
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
