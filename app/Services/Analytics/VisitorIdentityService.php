<?php

namespace App\Services\Analytics;

use App\Models\Analytics\AnalyticsSessionModel;
use App\Models\Analytics\AnalyticsVisitorModel;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

/**
 * Resolves the visitor/session pair for a tracking request. Client-supplied
 * UUIDs are treated as a hint, not authority: they're validated and looked
 * up, but staleness/format checks happen here, server-side - the client
 * can't force a session to stay alive past the inactivity window, and a
 * malformed or unknown UUID just results in a fresh row rather than an
 * error, since there's no login/security boundary being crossed.
 */
class VisitorIdentityService
{
    public function __construct(
        private readonly DeviceParser $deviceParser,
        private readonly TrafficSourceResolver $trafficSourceResolver,
    ) {}

    public function resolveVisitor(?string $clientUuid): AnalyticsVisitorModel
    {
        $uuid = $this->validUuid($clientUuid);
        $visitor = $uuid ? AnalyticsVisitorModel::where('visitor_uuid', $uuid)->first() : null;

        if ($visitor) {
            $visitor->last_seen_at = now();
            $visitor->save();

            return $visitor;
        }

        $uuid ??= (string) Str::uuid();

        try {
            return AnalyticsVisitorModel::create([
                'visitor_uuid' => $uuid,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
                'total_sessions' => 0,
            ]);
        } catch (UniqueConstraintViolationException) {
            // Lost a race with a concurrent request creating the same
            // visitor (e.g. two tracking calls firing close together
            // before the first response updates the client's cookie).
            // The row exists now - use it instead of failing the request.
            return AnalyticsVisitorModel::where('visitor_uuid', $uuid)->firstOrFail();
        }
    }

    /**
     * @param  array{path?: string, user_agent?: ?string, referrer?: ?string, query_string?: ?string, current_host?: string, screen_width?: ?int, screen_height?: ?int, ip_hash: string}  $context
     */
    public function resolveSession(AnalyticsVisitorModel $visitor, ?string $clientSessionUuid, array $context): AnalyticsSessionModel
    {
        $uuid = $this->validUuid($clientSessionUuid);
        $session = $uuid ? AnalyticsSessionModel::where('session_uuid', $uuid)->first() : null;

        $timeoutMinutes = config('analytics.session_timeout_minutes', 30);

        if ($session && $session->last_activity_at->lt(now()->subMinutes($timeoutMinutes))) {
            // Stale - the 30-minute inactivity window has passed. This is
            // the actual enforcement point for the session timeout; the
            // client's own cookie expiry is just an optimization to avoid
            // unnecessary lookups, not the source of truth. Don't reuse
            // the old uuid for the replacement session either - mint a
            // clean one.
            $session = null;
            $uuid = null;
        }

        // A well-formed uuid that simply isn't in the database yet is a
        // legitimate brand-new visit, not a rotation - reuse it rather
        // than discarding it for a server-minted one. Only a missing or
        // malformed client uuid (or a stale one, handled above) falls
        // back to a fresh server-generated id.
        return $session ?? $this->createSession($visitor, $context, $uuid);
    }

    public function touchSession(AnalyticsSessionModel $session, bool $isPageView = false, ?string $exitPage = null): void
    {
        $session->last_activity_at = now();

        if ($isPageView) {
            $session->page_view_count++;
        }

        if ($exitPage !== null) {
            $session->exit_page = $exitPage;
        }

        $session->save();
    }

    /**
     * @param  array{path?: string, user_agent?: ?string, referrer?: ?string, query_string?: ?string, current_host?: string, screen_width?: ?int, screen_height?: ?int, ip_hash: string}  $context
     */
    private function createSession(AnalyticsVisitorModel $visitor, array $context, ?string $uuid = null): AnalyticsSessionModel
    {
        $device = $this->deviceParser->parse($context['user_agent'] ?? null);
        $traffic = $this->trafficSourceResolver->resolve(
            $context['referrer'] ?? null,
            $context['query_string'] ?? null,
            $context['current_host'] ?? '',
        );

        $uuid ??= (string) Str::uuid();

        try {
            $session = AnalyticsSessionModel::create([
                'session_uuid' => $uuid,
                'visitor_id' => $visitor->id,
                'started_at' => now(),
                'last_activity_at' => now(),
                'entry_page' => $context['path'] ?? '/',
                'device_type' => $device['device_type'],
                'browser' => $device['browser'],
                'browser_version' => $device['browser_version'],
                'os' => $device['os'],
                'os_version' => $device['os_version'],
                'screen_width' => $context['screen_width'] ?? null,
                'screen_height' => $context['screen_height'] ?? null,
                'traffic_source' => $traffic['traffic_source'],
                'referrer_domain' => $traffic['referrer_domain'],
                'referrer_url' => $context['referrer'] ?? null,
                'utm_source' => $traffic['utm_source'],
                'utm_medium' => $traffic['utm_medium'],
                'utm_campaign' => $traffic['utm_campaign'],
                'utm_term' => $traffic['utm_term'],
                'utm_content' => $traffic['utm_content'],
                'ip_hash' => $context['ip_hash'],
            ]);
        } catch (UniqueConstraintViolationException) {
            // Same race as resolveVisitor() above, for session_uuid. Don't
            // double-count total_sessions here - only the request whose
            // insert actually succeeded increments it.
            return AnalyticsSessionModel::where('session_uuid', $uuid)->firstOrFail();
        }

        $visitor->increment('total_sessions');

        return $session;
    }

    private function validUuid(?string $value): ?string
    {
        return $value && Str::isUuid($value) ? $value : null;
    }
}
