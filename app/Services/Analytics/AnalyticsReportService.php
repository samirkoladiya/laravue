<?php

namespace App\Services\Analytics;

use App\Models\Analytics\AnalyticsEventModel;
use App\Models\Analytics\AnalyticsPageViewModel;
use App\Models\Analytics\AnalyticsSessionModel;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;

/**
 * The dashboard's read path. Queries the raw analytics_* tables directly
 * for every report rather than reading from analytics_daily_stats -
 * that table only gets populated once the Phase 9 aggregation command is
 * actually scheduled to run (a manual Windows Task Scheduler step), so
 * relying on it here would leave the dashboard showing nothing for weeks
 * on a fresh install. Raw-table queries are fast enough at this site's
 * scale given the indexes from the schema migrations; analytics_daily_stats
 * remains useful as a long-term summary once raw rows age out under
 * retention cleanup.
 */
class AnalyticsReportService
{
    /**
     * @return array{page_views: int, unique_visitors: int, sessions: int, leads: int, avg_session_duration_seconds: ?int, bounce_rate: ?float, conversion_rate: ?float}
     */
    public function summary(CarbonPeriod $range): array
    {
        [$start, $end] = [$range->getStartDate(), $range->getEndDate()];

        $pageViews = AnalyticsPageViewModel::whereBetween('viewed_at', [$start, $end])->count();
        $uniqueVisitors = AnalyticsPageViewModel::whereBetween('viewed_at', [$start, $end])
            ->distinct('visitor_id')->count('visitor_id');

        $sessionsQuery = fn () => AnalyticsSessionModel::whereBetween('started_at', [$start, $end]);
        $sessions = $sessionsQuery()->count();
        $avgDuration = $sessionsQuery()->whereNotNull('duration_seconds')->avg('duration_seconds');
        $closedSessions = $sessionsQuery()->whereNotNull('is_bounce')->count();
        $bounces = $sessionsQuery()->where('is_bounce', true)->count();

        $leads = AnalyticsEventModel::whereBetween('occurred_at', [$start, $end])
            ->where('is_conversion', true)->count();

        return [
            'page_views' => $pageViews,
            'unique_visitors' => $uniqueVisitors,
            'sessions' => $sessions,
            'leads' => $leads,
            'avg_session_duration_seconds' => $avgDuration !== null ? (int) round($avgDuration) : null,
            'bounce_rate' => $closedSessions > 0 ? round(($bounces / $closedSessions) * 100, 2) : null,
            'conversion_rate' => $sessions > 0 ? round(($leads / $sessions) * 100, 2) : null,
        ];
    }

    /**
     * @return array<int, array{date: string, page_views: int, unique_visitors: int, sessions: int}>
     */
    public function trafficOverTime(CarbonPeriod $range): array
    {
        [$start, $end] = [$range->getStartDate(), $range->getEndDate()];

        $pageViewRows = AnalyticsPageViewModel::selectRaw('DATE(viewed_at) as date, COUNT(*) as page_views, COUNT(DISTINCT visitor_id) as unique_visitors')
            ->whereBetween('viewed_at', [$start, $end])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $sessionRows = AnalyticsSessionModel::selectRaw('DATE(started_at) as date, COUNT(*) as sessions')
            ->whereBetween('started_at', [$start, $end])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        return collect(iterator_to_array($range))
            ->map(function ($day) use ($pageViewRows, $sessionRows) {
                $key = $day->toDateString();
                $pv = $pageViewRows->get($key);
                $s = $sessionRows->get($key);

                return [
                    'date' => $key,
                    'page_views' => $pv ? (int) $pv->page_views : 0,
                    'unique_visitors' => $pv ? (int) $pv->unique_visitors : 0,
                    'sessions' => $s ? (int) $s->sessions : 0,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{source: string, sessions: int}>
     */
    public function trafficSources(CarbonPeriod $range): array
    {
        return AnalyticsSessionModel::selectRaw('traffic_source, COUNT(*) as sessions')
            ->whereBetween('started_at', [$range->getStartDate(), $range->getEndDate()])
            ->groupBy('traffic_source')
            ->orderByDesc('sessions')
            ->get()
            ->map(fn ($row) => ['source' => $row->traffic_source, 'sessions' => (int) $row->sessions])
            ->all();
    }

    /**
     * @return array<int, array{path: string, views: int}>
     */
    public function topPages(CarbonPeriod $range, int $limit = 10): array
    {
        return AnalyticsPageViewModel::selectRaw('path, COUNT(*) as views')
            ->whereBetween('viewed_at', [$range->getStartDate(), $range->getEndDate()])
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => ['path' => $row->path, 'views' => (int) $row->views])
            ->all();
    }

    /**
     * @return array<int, array{path: string, sessions: int}>
     */
    public function landingPages(CarbonPeriod $range, int $limit = 10): array
    {
        return AnalyticsSessionModel::selectRaw('entry_page as path, COUNT(*) as sessions')
            ->whereBetween('started_at', [$range->getStartDate(), $range->getEndDate()])
            ->groupBy('entry_page')
            ->orderByDesc('sessions')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => ['path' => $row->path, 'sessions' => (int) $row->sessions])
            ->all();
    }

    /**
     * @return array<int, array{path: string, sessions: int}>
     */
    public function exitPages(CarbonPeriod $range, int $limit = 10): array
    {
        return AnalyticsSessionModel::selectRaw('exit_page as path, COUNT(*) as sessions')
            ->whereBetween('started_at', [$range->getStartDate(), $range->getEndDate()])
            ->whereNotNull('exit_page')
            ->groupBy('exit_page')
            ->orderByDesc('sessions')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => ['path' => $row->path, 'sessions' => (int) $row->sessions])
            ->all();
    }

    /**
     * @return array{device_type: array<int, array{label: string, sessions: int}>, browser: array<int, array{label: string, sessions: int}>, os: array<int, array{label: string, sessions: int}>}
     */
    public function deviceBreakdown(CarbonPeriod $range): array
    {
        [$start, $end] = [$range->getStartDate(), $range->getEndDate()];
        $base = fn () => AnalyticsSessionModel::whereBetween('started_at', [$start, $end]);

        $group = fn (string $column, int $limit = 8) => $base()
            ->whereNotNull($column)
            ->selectRaw("{$column} as label, COUNT(*) as sessions")
            ->groupBy($column)
            ->orderByDesc('sessions')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => ['label' => $row->label, 'sessions' => (int) $row->sessions])
            ->all();

        return [
            'device_type' => $group('device_type'),
            'browser' => $group('browser'),
            'os' => $group('os'),
        ];
    }

    /**
     * @return array<int, array{event_name: string, total: int, is_conversion: bool}>
     */
    public function conversions(CarbonPeriod $range): array
    {
        return AnalyticsEventModel::selectRaw('event_name, COUNT(*) as total, MAX(is_conversion) as is_conversion')
            ->whereBetween('occurred_at', [$range->getStartDate(), $range->getEndDate()])
            ->groupBy('event_name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'event_name' => $row->event_name,
                'total' => (int) $row->total,
                'is_conversion' => (bool) $row->is_conversion,
            ])
            ->all();
    }

    public function onlineNow(): int
    {
        return Cache::remember('analytics:online_now', 15, function () {
            return AnalyticsSessionModel::query()
                ->where('last_activity_at', '>=', now()->subMinutes(config('analytics.online_window_minutes', 5)))
                ->whereNull('ended_at')
                ->count();
        });
    }
}
