<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'instrument',
        'entry_date',
        'entry_price',
        'stop_price',
        'target_price',
        'notes',
        'setup_grade',
        'screenshot_path',
        'outcome',
    ];

    // Add any helper methods if needed
}
