<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'user_branch_id',
        'user_branch_name',
        'user_created_by',
        'action',
        'description'
    ];

    protected static function booted(): void
    {
        static::creating(function (ActivityLog $activityLog) {
            if (! $activityLog->user_id || $activityLog->user_name) {
                return;
            }

            $user = User::with('branch')->find($activityLog->user_id);

            if (! $user) {
                return;
            }

            $activityLog->user_name = $user->name;
            $activityLog->user_email = $user->email;
            $activityLog->user_role = $user->role;
            $activityLog->user_branch_id = $user->branch_id;
            $activityLog->user_branch_name = $user->branch?->name;
            $activityLog->user_created_by = $user->created_by;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actorName(): string
    {
        return $this->user_name ?? $this->user?->name ?? 'Deleted user';
    }

    public function actorRole(): string
    {
        return $this->user_role ?? $this->user?->role ?? 'unknown';
    }

    public function actorBranchName(): ?string
    {
        return $this->user_branch_name ?? $this->user?->branch?->name;
    }
}
