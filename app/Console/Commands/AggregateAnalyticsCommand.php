<?php

namespace App\Console\Commands;

use App\Models\Analytics\AnalyticsDailyStatModel;
use App\Services\Analytics\AnalyticsReportService;
use Carbon\CarbonPeriod;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('analytics:aggregate {--date= : The date to aggregate (Y-m-d), defaults to yesterday}')]
#[Description('Roll up raw analytics data into a daily summary row')]
class AggregateAnalyticsCommand extends Command
{
    public function __construct(private readonly AnalyticsReportService $reportService)
    {
        parent::__construct();
    }

    /**
     * Reuses AnalyticsReportService::summary() (the same computation the
     * dashboard itself uses) rather than duplicating the query logic here.
     */
    public function handle(): int
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now()->subDay();
        $range = CarbonPeriod::create($date->copy()->startOfDay(), $date->copy()->endOfDay());

        $summary = $this->reportService->summary($range);

        AnalyticsDailyStatModel::updateOrCreate(
            ['stat_date' => $date->toDateString()],
            [
                'page_views' => $summary['page_views'],
                'unique_visitors' => $summary['unique_visitors'],
                'sessions' => $summary['sessions'],
                'leads' => $summary['leads'],
                'avg_session_duration_seconds' => $summary['avg_session_duration_seconds'],
                'bounce_rate' => $summary['bounce_rate'],
            ],
        );

        $this->info("Aggregated analytics for {$date->toDateString()}.");

        return self::SUCCESS;
    }
}
