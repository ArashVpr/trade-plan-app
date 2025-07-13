<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    // protected $fillable = ['user_id', 'zone_qualifiers', 'technicals', 'fundamentals', 'score', 'asset', 'notes'];
    // protected $casts = [
    //     'zone_qualifiers' => 'array',
    //     'technicals' => 'array',
    //     'fundamentals' => 'array',
    // ];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}