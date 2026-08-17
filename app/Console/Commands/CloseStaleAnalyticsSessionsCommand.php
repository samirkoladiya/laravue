<?php

namespace App\Console\Commands;

use App\Models\Analytics\AnalyticsSessionModel;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('analytics:close-stale-sessions')]
#[Description('Close analytics sessions that have gone past the inactivity timeout window')]
class CloseStaleAnalyticsSessionsCommand extends Command
{
    /**
     * exit_page is kept current on every page view already (see
     * AnalyticsRecorder::recordPageView), so only duration_seconds and
     * is_bounce need computing here at close time.
     */
    public function handle(): int
    {
        $cutoff = now()->subMinutes(config('analytics.session_timeout_minutes', 30));
        $closed = 0;

        AnalyticsSessionModel::query()
            ->whereNull('ended_at')
            ->where('last_activity_at', '<', $cutoff)
            ->chunkById(200, function ($sessions) use (&$closed) {
                foreach ($sessions as $session) {
                    $session->update([
                        'ended_at' => $session->last_activity_at,
                        'duration_seconds' => $session->started_at->diffInSeconds($session->last_activity_at),
                        'is_bounce' => $session->page_view_count <= 1,
                    ]);

                    $closed++;
                }
            });

        $this->info("Closed {$closed} stale session(s).");

        return self::SUCCESS;
    }
}
