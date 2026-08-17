<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'session_id',
    'visitor_id',
    'path',
    'title',
    'viewed_at',
    'duration_seconds',
])]
class AnalyticsPageViewModel extends Model
{
    protected $table = 'analytics_page_views';

    // Write-once rows - no updated_at column.
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'duration_seconds' => 'integer',
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
