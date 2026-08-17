<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\Honeypot;
use App\Http\Requests\Concerns\SanitizesText;
use Illuminate\Foundation\Http\FormRequest;

class AdminFaqRequest extends FormRequest
{
    use Honeypot, SanitizesText;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'question' => $this->sanitizeSingleLine($this->input('question')),
            'answer' => $this->sanitizeMultiLine($this->input('answer')),
            'status' => $this->boolean('status'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'status' => ['required', 'boolean'],
            'website' => ['nullable'], // honeypot, see Concerns\Honeypot
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question.required' => 'Please enter a question.',
            'answer.required' => 'Please enter an answer.',
        ];
    }
}
