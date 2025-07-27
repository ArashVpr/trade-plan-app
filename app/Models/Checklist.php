<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'technicals' => 'array',
        'fundamentals' => 'array',
        'zone_qualifiers' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tradeEntry()
    {
        return $this->hasOne(TradeEntry::class);
    }
}
