<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'session_id',
    'visitor_id',
    'event_name',
    'event_data',
    'is_conversion',
    'occurred_at',
])]
class AnalyticsEventModel extends Model
{
    protected $table = 'analytics_events';

    // Write-once rows - no updated_at column.
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'event_data' => 'array',
            'is_conversion' => 'boolean',
            'occurred_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AnalyticsSessionModel::class, 'session_id');
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnalyticsVisitorModel::class, 'visitor_id');
    }
}
