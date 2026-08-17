<?php

namespace App\Services\Analytics;

use App\Models\Analytics\AnalyticsEventModel;
use App\Models\Analytics\AnalyticsPageViewModel;
use App\Models\Analytics\AnalyticsSessionModel;
use App\Models\Analytics\AnalyticsVisitorModel;
use App\Models\InquiryModel;
use Illuminate\Support\Str;

/**
 * The write path. Takes a plain array of already-extracted request data
 * (deliberately not an Illuminate\Http\Request - keeps this class
 * decoupled from the HTTP layer and testable without a fake request) and
 * orchestrates visitor/session resolution plus the actual insert.
 */
class AnalyticsRecorder
{
    /**
     * Events flagged as a lead. Contact-intent clicks (WhatsApp/phone/email)
     * are tracked as engagement signals but deliberately not counted as
     * conversions - only an actual form submission is a lead.
     */
    private const CONVERSION_EVENTS = ['contact_form_submitted'];

    public function __construct(
        private readonly VisitorIdentityService $identity,
    ) {}

    /**
     * @param  array{visitor_uuid?: ?string, session_uuid?: ?string, ip: string, user_agent?: ?string, referrer?: ?string, query_string?: ?string, current_host?: string, path: string, title?: ?string, screen_width?: ?int, screen_height?: ?int}  $input
     * @return array{visitor_id: string, session_id: string}
     */
    public function recordPageView(array $input): array
    {
        [$visitor, $session] = $this->resolveVisitorAndSession($input);

        AnalyticsPageViewModel::create([
            'session_id' => $session->id,
            'visitor_id' => $visitor->id,
            'path' => $input['path'] ?? '/',
            'title' => $input['title'] ?? null,
            'viewed_at' => now(),
        ]);

        $this->identity->touchSession($session, isPageView: true, exitPage: $input['path'] ?? '/');

        return $this->identityResponse($visitor, $session);
    }

    /**
     * @param  array{visitor_uuid?: ?string, session_uuid?: ?string, ip: string, user_agent?: ?string, referrer?: ?string, query_string?: ?string, current_host?: string, path: string, event_name: string, event_data?: ?array}  $input
     * @return array{visitor_id: string, session_id: string}
     */
    public function recordEvent(array $input): array
    {
        [$visitor, $session] = $this->resolveVisitorAndSession($input);

        $eventName = $input['event_name'];

        AnalyticsEventModel::create([
            'session_id' => $session->id,
            'visitor_id' => $visitor->id,
            'event_name' => $eventName,
            'event_data' => $input['event_data'] ?? null,
            'is_conversion' => in_array($eventName, self::CONVERSION_EVENTS, true),
            'occurred_at' => now(),
        ]);

        $this->identity->touchSession($session);

        return $this->identityResponse($visitor, $session);
    }

    /**
     * Links a submitted contact_inquiry back to its analytics session and
     * fires the contact_form_submitted conversion event. Called from
     * InquiryController::store() - must never throw, since a broken/absent
     * analytics session must not block the actual inquiry submission.
     */
    public function recordConversionForInquiry(InquiryModel $inquiry, ?string $sessionUuid): void
    {
        if (! $sessionUuid || ! Str::isUuid($sessionUuid)) {
            return;
        }

        $session = AnalyticsSessionModel::where('session_uuid', $sessionUuid)->first();

        if (! $session) {
            return;
        }

        $inquiry->update(['analytics_session_id' => $session->id]);

        AnalyticsEventModel::create([
            'session_id' => $session->id,
            'visitor_id' => $session->visitor_id,
            'event_name' => 'contact_form_submitted',
            'event_data' => ['inquiry_id' => $inquiry->id],
            'is_conversion' => true,
            'occurred_at' => now(),
        ]);

        $this->identity->touchSession($session);
    }

    /**
     * @return array{0: AnalyticsVisitorModel, 1: AnalyticsSessionModel}
     */
    private function resolveVisitorAndSession(array $input): array
    {
        $visitor = $this->identity->resolveVisitor($input['visitor_uuid'] ?? null);

        $context = [
            'path' => $input['path'] ?? '/',
            'user_agent' => $input['user_agent'] ?? null,
            'referrer' => $input['referrer'] ?? null,
            'query_string' => $input['query_string'] ?? null,
            'current_host' => $input['current_host'] ?? '',
            'screen_width' => $input['screen_width'] ?? null,
            'screen_height' => $input['screen_height'] ?? null,
            'ip_hash' => hash_hmac('sha256', $input['ip'], config('analytics.ip_hash_key')),
        ];

        $session = $this->identity->resolveSession($visitor, $input['session_uuid'] ?? null, $context);

        return [$visitor, $session];
    }

    /**
     * @return array{visitor_id: string, session_id: string}
     */
    private function identityResponse(AnalyticsVisitorModel $visitor, AnalyticsSessionModel $session): array
    {
        return [
            'visitor_id' => $visitor->visitor_uuid,
            'session_id' => $session->session_uuid,
        ];
    }
}
