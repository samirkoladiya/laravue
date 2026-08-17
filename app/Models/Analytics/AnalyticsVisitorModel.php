<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'visitor_uuid',
    'first_seen_at',
    'last_seen_at',
    'total_sessions',
    'country',
    'city',
])]
class AnalyticsVisitorModel extends Model
{
    protected $table = 'analytics_visitors';

    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'total_sessions' => 'integer',
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AnalyticsSessionModel::class, 'visitor_id');
    }
}
