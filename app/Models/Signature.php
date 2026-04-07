<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $fillable = ['member_id', 'image_path', 'created_by'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
