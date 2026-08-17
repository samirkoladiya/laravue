<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'stat_date',
    'page_views',
    'unique_visitors',
    'sessions',
    'leads',
    'avg_session_duration_seconds',
    'bounce_rate',
])]
class AnalyticsDailyStatModel extends Model
{
    protected $table = 'analytics_daily_stats';

    protected function casts(): array
    {
        return [
            'stat_date' => 'date',
            'page_views' => 'integer',
            'unique_visitors' => 'integer',
            'sessions' => 'integer',
            'leads' => 'integer',
            'avg_session_duration_seconds' => 'integer',
            'bounce_rate' => 'decimal:2',
        ];
    }
}
