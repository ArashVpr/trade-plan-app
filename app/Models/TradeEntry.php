<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'screenshot_paths' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function managementItems()
    {
        return $this->hasMany(TradeManagementItem::class);
    }


    /**
     * Check if trade is a win
     */
    public function isWin()
    {
        return $this->trade_status === 'win';
    }

    /**
     * Check if trade is a loss
     */
    public function isLoss()
    {
        return $this->trade_status === 'loss';
    }

    /**
     * Check if trade is breakeven
     */
    public function isBreakeven()
    {
        return $this->trade_status === 'breakeven';
    }
}
