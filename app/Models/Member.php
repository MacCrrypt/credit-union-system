<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Member extends Model
{
    protected $fillable = ['account_number', 'name', 'phone'];

    protected static function booted()
    {
        static::creating(function (Member $member) {
            $member->slug = $member->generateSlug($member->name);
        });

        static::updating(function (Member $member) {
            if ($member->isDirty('name')) {
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
}
