<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'checklist_id',
        'entry_date',
        'position_type',
        'entry_price',
        'stop_price',
        'target_price',
        'rrr',
        'notes',
        'screenshot_paths',
        'trade_status',
    ];

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

    /**
     * Get the first screenshot path (for backward compatibility)
     */
    public function getScreenshotPathAttribute()
    {
        $paths = $this->screenshot_paths;

        return is_array($paths) && count($paths) > 0 ? $paths[0] : null;
    }
}
