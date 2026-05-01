<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $members = DB::table('members')
            ->join('signatures', 'signatures.member_id', '=', 'members.id')
            ->whereNull('members.created_by')
            ->whereNotNull('signatures.created_by')
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
        // This is a data repair migration. Reversing it would orphan valid records again.
    }
};
