<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'zone_qualifiers',
        'technicals',
        'fundamentals',
        'exclude_fundamentals',
        'score',
        'symbol',
    ];

    protected $casts = [
        'technicals' => 'array',
        'fundamentals' => 'array',
        'zone_qualifiers' => 'array',
        'exclude_fundamentals' => 'boolean',
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
