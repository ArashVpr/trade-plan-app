<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistSetting extends Model
{
    protected $fillable = ['user_id', 'scoring_weights'];

    protected $casts = ['scoring_weights' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
