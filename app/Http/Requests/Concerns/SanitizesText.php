<?php

namespace App\Http\Requests\Concerns;

/**
 * Strips HTML tags before validation as defense-in-depth against stored
 * XSS, even where a value isn't rendered unescaped today.
 */
trait SanitizesText
{
    private function sanitizeSingleLine(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return trim(str_replace(["\r", "\n"], ' ', strip_tags($value)));
    }

    private function sanitizeMultiLine(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return trim(strip_tags($value));
    }
}
