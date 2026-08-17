<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnalyticsTrackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitize input before validation runs.
     *
     * Defense-in-depth against stored XSS: HTML tags are stripped from
     * every free-text field so a `<script>`/`onerror=` payload can never
     * reach the database, even if a future dashboard view rendered this
     * data without escaping it. event_data values are stripped
     * recursively for the same reason.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'path' => $this->sanitize($this->input('path')),
            'title' => $this->sanitize($this->input('title')),
            'event_data' => $this->filled('event_data') && is_array($this->input('event_data'))
                ? $this->sanitizeDeep($this->input('event_data'))
                : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'visitor_id' => ['nullable', 'string', 'uuid'],
            'session_id' => ['nullable', 'string', 'uuid'],

            'type' => ['required', Rule::in(['page_view', 'event'])],

            'path' => ['required_if:type,page_view', 'nullable', 'string', 'max:191'],
            'title' => ['nullable', 'string', 'max:191'],

            'event_name' => ['required_if:type,event', 'nullable', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/'],
            'event_data' => ['nullable', 'array', function ($attribute, $value, $fail) {
                if ($value !== null && strlen(json_encode($value)) > 2048) {
                    $fail('Event data is too large.');
                }
            }],

            'referrer' => ['nullable', 'string', 'max:2048'],
            'query_string' => ['nullable', 'string', 'max:2048'],

            'screen' => ['nullable', 'array'],
            'screen.width' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'screen.height' => ['nullable', 'integer', 'min:0', 'max:20000'],
        ];
    }

    private function sanitize(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return trim(str_replace(["\r", "\n"], ' ', strip_tags($value)));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function sanitizeDeep(array $data): array
    {
        return collect($data)->map(function ($value) {
            if (is_string($value)) {
                return $this->sanitize($value);
            }

            if (is_array($value)) {
                return $this->sanitizeDeep($value);
            }

            return $value;
        })->all();
    }
}
