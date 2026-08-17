<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactInquiryRequest;
use App\Models\InquiryModel;
use App\Services\Analytics\AnalyticsRecorder;
use Illuminate\Http\RedirectResponse;
use Throwable;

class InquiryController extends Controller
{
    public function store(ContactInquiryRequest $request, AnalyticsRecorder $recorder): RedirectResponse
    {
        // Bots that trip the honeypot get the same "success" response as a
        // real submission, without being persisted, so they have no signal
        // that they were caught.
        if (! $request->isSpam()) {
            // analytics_session_id here is the client's session *uuid*, not
            // the analytics_sessions.id foreign key - it's resolved and
            // linked separately below, never mass-assigned directly.
            $inquiry = InquiryModel::create($request->safe()->only(['name', 'email', 'subject', 'message']));

            // Never let a broken/absent analytics session block or error
            // the actual inquiry submission - recordConversionForInquiry()
            // already no-ops on a missing/invalid session, but this is a
            // "nice to have, must never break the core feature" integration
            // point, so any unexpected failure is swallowed too.
            try {
                $recorder->recordConversionForInquiry(
                    $inquiry,
                    $request->safe()->string('analytics_session_id')->value() ?: null,
                );
            } catch (Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Your message has been sent. Thank you!');
    }
}
