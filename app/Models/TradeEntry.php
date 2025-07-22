<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $fillable = [
    //     'user_id',
    //     'checklist_id',
    //     'instrument',
    //     'position_type',
    //     'entry_date',
    //     'entry_price',
    //     'stop_price',
    //     'target_price',
    //     'notes',
    //     'screenshot_path',
    //     'outcome',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
