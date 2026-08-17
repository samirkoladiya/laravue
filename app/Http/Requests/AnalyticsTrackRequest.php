<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesText;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnalyticsTrackRequest extends FormRequest
{
    use SanitizesText;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * event_data values are stripped recursively for the same reason as
     * path/title - defense-in-depth against stored XSS reaching a future
     * dashboard view.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'path' => $this->sanitizeSingleLine($this->input('path')),
            'title' => $this->sanitizeSingleLine($this->input('title')),
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function sanitizeDeep(array $data): array
    {
        return collect($data)->map(function ($value) {
            if (is_string($value)) {
                return $this->sanitizeSingleLine($value);
            }

            if (is_array($value)) {
                return $this->sanitizeDeep($value);
            }

            return $value;
        })->all();
    }
}
