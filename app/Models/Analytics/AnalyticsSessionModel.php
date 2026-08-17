<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'session_uuid',
    'visitor_id',
    'started_at',
    'last_activity_at',
    'ended_at',
    'duration_seconds',
    'entry_page',
    'exit_page',
    'page_view_count',
    'is_bounce',
    'device_type',
    'browser',
    'browser_version',
    'os',
    'os_version',
    'screen_width',
    'screen_height',
    'traffic_source',
    'referrer_domain',
    'referrer_url',
    'utm_source',
    'utm_medium',
    'utm_campaign',
    'utm_term',
    'utm_content',
    'ip_hash',
    'country',
    'city',
])]
class AnalyticsSessionModel extends Model
{
    protected $table = 'analytics_sessions';

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'ended_at' => 'datetime',
            'duration_seconds' => 'integer',
            'page_view_count' => 'integer',
            'is_bounce' => 'boolean',
            'screen_width' => 'integer',
            'screen_height' => 'integer',
        ];
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnalyticsVisitorModel::class, 'visitor_id');
    }

    public function pageViews(): HasMany
    {
        return $this->hasMany(AnalyticsPageViewModel::class, 'session_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AnalyticsEventModel::class, 'session_id');
    }
}
