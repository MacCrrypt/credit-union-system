<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('activity-logs:prune {days=365}', function (int $days) {
    $cutoff = now()->subDays($days);

    // Log pruning is a lightweight retention tool so the activity table does not
    // grow forever on production servers.
    $deleted = DB::table('activity_logs')
        ->where('created_at', '<', $cutoff)
        ->delete();

    $this->info("Deleted {$deleted} activity logs older than {$days} days.");
})->purpose('Prune old activity logs to control database growth.');

Artisan::command('signatures:migrate-private', function () {
    $migrated = 0;
    $skipped = 0;
    $targetDisk = config('filesystems.signature_cards.disk', 'local');

    DB::table('signatures')
        ->select('id', 'image_path')
        ->orderBy('id')
        ->chunkById(100, function ($signatures) use (&$migrated, &$skipped, $targetDisk) {
            foreach ($signatures as $signature) {
                if (Storage::disk($targetDisk)->exists($signature->image_path)) {
                    $skipped++;
                    continue;
                }

                if (! Storage::disk('public')->exists($signature->image_path)) {
                    $skipped++;
                    continue;
                }

                Storage::disk($targetDisk)->makeDirectory(dirname($signature->image_path));
                Storage::disk($targetDisk)->put(
                    $signature->image_path,
                    Storage::disk('public')->get($signature->image_path)
                );

                Storage::disk('public')->delete($signature->image_path);
                $migrated++;
            }
        });

    $this->info("Migrated {$migrated} signature files to {$targetDisk} storage. Skipped {$skipped} files.");
})->purpose('Move legacy signature files from public storage to private storage.');
