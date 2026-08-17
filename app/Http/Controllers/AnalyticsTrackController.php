<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalyticsTrackRequest;
use App\Services\Analytics\AnalyticsRecorder;
use Illuminate\Http\JsonResponse;

class AnalyticsTrackController extends Controller
{
    public function store(AnalyticsTrackRequest $request, AnalyticsRecorder $recorder): JsonResponse
    {
        $validated = $request->validated();

        $input = [
            'visitor_uuid' => $validated['visitor_id'] ?? null,
            'session_uuid' => $validated['session_id'] ?? null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $validated['referrer'] ?? null,
            'query_string' => $validated['query_string'] ?? null,
            'current_host' => $request->getHost(),
            'path' => $validated['path'] ?? $request->path(),
            'title' => $validated['title'] ?? null,
            'screen_width' => $validated['screen']['width'] ?? null,
            'screen_height' => $validated['screen']['height'] ?? null,
        ];

        $result = $validated['type'] === 'event'
            ? $recorder->recordEvent([
                ...$input,
                'event_name' => $validated['event_name'],
                'event_data' => $validated['event_data'] ?? null,
            ])
            : $recorder->recordPageView($input);

        return response()->json($result);
    }
}
