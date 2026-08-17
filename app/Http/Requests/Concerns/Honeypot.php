<?php

namespace App\Http\Requests\Concerns;

/**
 * Spam check for a hidden "website" field real users never fill in.
 * Pair with a `'website' => ['nullable']` rule - it must stay
 * unconstrained, or a bot's malformed input would surface a clue that
 * the field is being checked.
 */
trait Honeypot
{
    public function isSpam(): bool
    {
        return $this->filled('website');
    }
}
