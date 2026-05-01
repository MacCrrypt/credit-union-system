<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Member extends Model
{
    protected $fillable = ['account_number', 'name', 'phone', 'created_by'];

    protected static function booted()
    {
        static::creating(function (Member $member) {
            // Member URLs use a slug so links stay readable instead of exposing raw ids.
            $member->slug = $member->generateSlug($member->name);
        });

        static::updating(function (Member $member) {
            if ($member->isDirty('name')) {
                // Slug follows name changes so the route stays human-readable.
                $member->slug = $member->generateSlug($member->name);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected function generateSlug(string $name): string
    {
        $baseSlug = Str::slug($name ?: $this->account_number ?: 'member');
        $slug = $baseSlug ?: 'member';
        $count = 1;

        // Keep incrementing until we have a unique slug for route model binding.
        while (self::where('slug', $slug)
            ->when($this->exists, fn ($query) => $query->where('id', '!=', $this->id))
            ->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function signature()
    {
        return $this->hasOne(Signature::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branchIdForAccess(): ?int
    {
        return $this->creator?->branch_id ?? $this->signature?->creator?->branch_id;
    }
}
