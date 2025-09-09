<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeManagementItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'due_date' => 'datetime',
        'triggered_at' => 'datetime',
    ];

    public function tradeEntry()
    {
        return $this->belongsTo(TradeEntry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the management item is due or overdue
     */
    public function isDue()
    {
        return $this->due_date && $this->due_date <= now();
    }

    /**
     * Check if the management item is overdue
     */
    public function isOverdue()
    {
        return $this->due_date && $this->due_date < now() && $this->status === 'pending';
    }

    /**
     * Mark the management item as completed
     */
    public function markCompleted($notes = null)
    {
        $this->update([
            'status' => 'completed',
            'triggered_at' => now(),
            'notes' => $notes ?: $this->notes,
        ]);
    }

    /**
     * Mark the management item as triggered
     */
    public function markTriggered($notes = null)
    {
        $this->update([
            'status' => 'triggered',
            'triggered_at' => now(),
            'notes' => $notes ?: $this->notes,
        ]);
    }

    /**
     * Get predefined management items
     */
    public static function getPredefinedItems()
    {
        return [
            'technical' => [
                [
                    'title' => 'Approaching Supply Zone',
                    'description' => 'Price is approaching a significant supply zone',
                    'priority' => 'high',
                ],
                [
                    'title' => 'Approaching Demand Zone',
                    'description' => 'Price is approaching a significant demand zone',
                    'priority' => 'high',
                ],
                [
                    'title' => 'Breaking Key Support',
                    'description' => 'Price has broken below key support level',
                    'priority' => 'critical',
                ],
                [
                    'title' => 'Breaking Key Resistance',
                    'description' => 'Price has broken above key resistance level',
                    'priority' => 'critical',
                ],
                [
                    'title' => 'Trend Line Break',
                    'description' => 'Price has broken the trend line',
                    'priority' => 'medium',
                ],
            ],
            'fundamental' => [
                [
                    'title' => 'News Event Within 24 Hours',
                    'description' => 'Important news event scheduled within next 24 hours',
                    'priority' => 'high',
                ],
                [
                    'title' => 'Economic Data Release',
                    'description' => 'Major economic data release affecting this instrument',
                    'priority' => 'medium',
                ],
                [
                    'title' => 'Earnings Announcement',
                    'description' => 'Company earnings announcement scheduled',
                    'priority' => 'high',
                ],
                [
                    'title' => 'Central Bank Meeting',
                    'description' => 'Central bank meeting/decision scheduled',
                    'priority' => 'high',
                ],
            ],
            'risk' => [
                [
                    'title' => 'Move Stop to Breakeven',
                    'description' => 'Trade has reached 1R profit, consider moving stop to breakeven',
                    'priority' => 'medium',
                ],
                [
                    'title' => 'Scale Out 50%',
                    'description' => 'Trade has reached first target, consider scaling out 50%',
                    'priority' => 'medium',
                ],
                [
                    'title' => 'Trail Stop Loss',
                    'description' => 'Start trailing stop loss as trade moves in favor',
                    'priority' => 'low',
                ],
                [
                    'title' => 'Max Risk Exceeded',
                    'description' => 'Trade has exceeded maximum risk threshold',
                    'priority' => 'critical',
                ],
            ],
            'time' => [
                [
                    'title' => 'Weekly Trade Review',
                    'description' => 'Review trade performance and adjust strategy if needed',
                    'priority' => 'low',
                ],
                [
                    'title' => 'End of Month Review',
                    'description' => 'Monthly assessment of trade performance',
                    'priority' => 'medium',
                ],
                [
                    'title' => 'Trade Timeout',
                    'description' => 'Trade has been open for predefined maximum time',
                    'priority' => 'medium',
                ],
            ]
        ];
    }
}
