<?php

namespace App\Console\Commands;

use App\Models\Analytics\AnalyticsEventModel;
use App\Models\Analytics\AnalyticsPageViewModel;
use App\Models\Analytics\AnalyticsSessionModel;
use App\Models\Analytics\AnalyticsVisitorModel;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

#[Signature('analytics:cleanup')]
#[Description('Delete analytics data past its configured retention window')]
class CleanupAnalyticsCommand extends Command
{
    /**
     * Each table is purged independently by its own column/window, even
     * though deleting an old session already cascades to its page_views/
     * events - explicit per-table purges stay correct if the windows are
     * ever configured to differ, and are clearer to read/log than relying
     * on cascade side effects alone. contact_inquiry rows are never
     * touched - the FK is nullOnDelete(), so purging an old session just
     * severs the analytics link, never deletes the inquiry.
     */
    public function handle(): int
    {
        $this->purge(AnalyticsPageViewModel::class, 'viewed_at', config('analytics.retention.page_views_days'));
        $this->purge(AnalyticsEventModel::class, 'occurred_at', config('analytics.retention.events_days'));
        $this->purge(AnalyticsSessionModel::class, 'started_at', config('analytics.retention.sessions_days'));
        $this->purge(AnalyticsVisitorModel::class, 'last_seen_at', config('analytics.retention.visitors_days'));

        return self::SUCCESS;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function purge(string $modelClass, string $column, ?int $days): void
    {
        if ($days === null) {
            return;
        }

        $cutoff = now()->subDays($days);
        $total = 0;

        // Chunked deletes (via LIMIT, not chunkById - the rows being
        // deleted shift under a cursor) to avoid a long lock on a live
        // table when the backlog is large.
        do {
            $deleted = $modelClass::where($column, '<', $cutoff)->limit(1000)->delete();
            $total += $deleted;
        } while ($deleted === 1000);

        $table = (new $modelClass)->getTable();
        $this->info("Deleted {$total} row(s) from {$table} older than {$days} days.");
    }
}
