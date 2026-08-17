<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminFaqModel;
use App\Models\Admin\AdminTeamModel;
use App\Models\InquiryModel;
use App\Services\Analytics\AnalyticsReportService;
use App\Services\Analytics\DateRangeResolver;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DateRangeResolver $dateRangeResolver,
        private readonly AnalyticsReportService $reportService,
    ) {}

    public function index(): Response
    {
        $range = $this->dateRangeResolver->resolve('7d');
        $summary = $this->reportService->summary($range);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'teamMembers' => AdminTeamModel::where('status', true)->count(),
                'faqs' => AdminFaqModel::where('status', true)->count(),
                'inquiries' => InquiryModel::count(),
                'visitors7d' => $summary['unique_visitors'],
            ],
            'trafficOverTime' => $this->reportService->trafficOverTime($range),
            'recentInquiries' => InquiryModel::latest()
                ->limit(5)
                ->get(['id', 'name', 'email', 'subject', 'created_at'])
                ->map(fn (InquiryModel $inquiry) => [
                    'id' => $inquiry->id,
                    'name' => $inquiry->name,
                    'email' => $inquiry->email,
                    'subject' => $inquiry->subject,
                    'created_at' => $inquiry->created_at->diffForHumans(),
                ]),
        ]);
    }
}
