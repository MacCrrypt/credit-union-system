<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        $members = DB::table('members')->get();

        foreach ($members as $member) {
            $baseSlug = Str::slug($member->name ?: $member->account_number ?: 'member');
            $slug = $baseSlug ?: 'member';
            $count = 1;

            while (DB::table('members')->where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }

            DB::table('members')
                ->where('id', $member->id)
                ->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
