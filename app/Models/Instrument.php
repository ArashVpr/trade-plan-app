<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    use HasFactory;

    protected $fillable = ['symbol', 'name', 'type', 'category', 'exchange', 'is_active'];

    // Scope to get only active instruments
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to filter by type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
