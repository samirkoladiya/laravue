<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\AnalyticsReportService;
use App\Services\Analytics\DateRangeResolver;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAnalyticsController extends Controller
{
    public function __construct(
        private readonly DateRangeResolver $dateRangeResolver,
        private readonly AnalyticsReportService $reportService,
    ) {}

    public function index(Request $request): Response
    {
        [$preset, $from, $to, $range] = $this->resolveRangeFromRequest($request);

        return Inertia::render('Admin/Analytics/Index', [
            'filters' => ['range' => $preset, 'from' => $from, 'to' => $to],
            'summary' => $this->reportService->summary($range),
            'trafficOverTime' => $this->reportService->trafficOverTime($range),
            'trafficSources' => $this->reportService->trafficSources($range),
            'topPages' => $this->reportService->topPages($range),
            'landingPages' => $this->reportService->landingPages($range),
            'exitPages' => $this->reportService->exitPages($range),
            'deviceBreakdown' => $this->reportService->deviceBreakdown($range),
            'conversions' => $this->reportService->conversions($range),
        ]);
    }

    /**
     * Plain JSON, not Inertia - this is a background poll target, not a
     * page navigation.
     */
    public function realtime(): JsonResponse
    {
        return response()->json(['online' => $this->reportService->onlineNow()]);
    }

    /**
     * One row per day in the selected range (the analytics_daily_stats
     * shape) - scoped to a single summary export in v1; other report
     * types (sources/pages/devices) can be added later behind a ?report=
     * param without new infrastructure.
     */
    public function export(Request $request): StreamedResponse
    {
        [, , , $range] = $this->resolveRangeFromRequest($request);

        $rows = collect(iterator_to_array($range))->map(function ($day) {
            $dayRange = CarbonPeriod::create($day->copy()->startOfDay(), $day->copy()->endOfDay());

            return ['date' => $day->toDateString(), ...$this->reportService->summary($dayRange)];
        });

        $filename = sprintf(
            'analytics-%s-to-%s.csv',
            $range->getStartDate()->toDateString(),
            $range->getEndDate()->toDateString(),
        );

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'Date', 'Page Views', 'Unique Visitors', 'Sessions', 'Leads',
                'Avg Session Duration (s)', 'Bounce Rate (%)', 'Conversion Rate (%)',
            ]);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['date'],
                    $row['page_views'],
                    $row['unique_visitors'],
                    $row['sessions'],
                    $row['leads'],
                    $row['avg_session_duration_seconds'] ?? '',
                    $row['bounce_rate'] ?? '',
                    $row['conversion_rate'] ?? '',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * @return array{0: string, 1: ?string, 2: ?string, 3: CarbonPeriod}
     */
    private function resolveRangeFromRequest(Request $request): array
    {
        $preset = $request->string('range', '7d')->value();
        $from = $request->string('from')->value() ?: null;
        $to = $request->string('to')->value() ?: null;

        return [$preset, $from, $to, $this->dateRangeResolver->resolve($preset, $from, $to)];
    }
}
