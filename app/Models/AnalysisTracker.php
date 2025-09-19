<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AnalysisTracker extends Model
{
    use HasFactory;

    protected $table = 'analysis_tracker';
    
    protected $guarded = [];

    protected $casts = [
        'tracked_metrics' => 'array',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tracker) {
            if (!$tracker->last_updated_at) {
                $tracker->last_updated_at = now();
            }
            // Ensure user_id is set
            if (!$tracker->user_id) {
                $tracker->user_id = 1; // Temporary fallback - replace with Auth::id() when auth is implemented
            }
        });
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate completion percentage based on tracked metrics
     */
    public function calculateCompletionPercentage(): int
    {
        $metrics = $this->tracked_metrics ?? [];
        $totalFields = 12; // 6 zone qualifiers + 2 technicals + 4 fundamentals
        $completedFields = 0;

        // Count zone qualifiers
        if (!empty($metrics['zone_qualifiers']) && is_array($metrics['zone_qualifiers'])) {
            $completedFields += min(count($metrics['zone_qualifiers']), 6);
        }

        // Count technical analysis fields
        if (!empty($metrics['technicals']['location'])) $completedFields++;
        if (!empty($metrics['technicals']['direction'])) $completedFields++;

        // Count fundamental analysis fields  
        if (!empty($metrics['fundamentals']['valuation'])) $completedFields++;
        if (!empty($metrics['fundamentals']['seasonalConfluence'])) $completedFields++;
        if (!empty($metrics['fundamentals']['nonCommercials'])) $completedFields++;
        if (!empty($metrics['fundamentals']['cotIndex'])) $completedFields++;

        return round(($completedFields / $totalFields) * 100);
    }

    /**
     * Update completion percentage and last_updated_at
     */
    public function updateCompletion()
    {
        $this->completion_percentage = $this->calculateCompletionPercentage();
        $this->last_updated_at = now();
        $this->save();
    }

    /**
     * Check if tracker is ready for checklist creation (>=70% complete)
     */
    public function isReadyForChecklist(): bool
    {
        return $this->completion_percentage >= 70;
    }

    /**
     * Get days since last update
     */
    public function getDaysSinceUpdate(): int
    {
        return (int) $this->last_updated_at->diffInDays(now());
    }

    /**
     * Scope for active trackers (updated within last 30 days)
     */
    public function scopeActive($query)
    {
        return $query->where('last_updated_at', '>=', now()->subDays(30));
    }

    /**
     * Scope for stale trackers (not updated in 7+ days)
     */
    public function scopeStale($query)
    {
        return $query->where('last_updated_at', '<=', now()->subDays(7));
    }
}
