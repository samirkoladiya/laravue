<?php

namespace App\Services\Analytics;

/**
 * Classifies a session's traffic source from its referrer and query
 * string. Domain/param lists live in config/analytics.php so they can be
 * extended without touching code.
 */
class TrafficSourceResolver
{
    /**
     * @return array{traffic_source: string, referrer_domain: ?string, utm_source: ?string, utm_medium: ?string, utm_campaign: ?string, utm_term: ?string, utm_content: ?string}
     */
    public function resolve(?string $referrerUrl, ?string $queryString, string $currentHost): array
    {
        $referrerDomain = $this->extractDomain($referrerUrl);
        $queryParams = $this->parseQueryString($queryString);

        $utm = [
            'utm_source' => $queryParams['utm_source'] ?? null,
            'utm_medium' => $queryParams['utm_medium'] ?? null,
            'utm_campaign' => $queryParams['utm_campaign'] ?? null,
            'utm_term' => $queryParams['utm_term'] ?? null,
            'utm_content' => $queryParams['utm_content'] ?? null,
        ];

        return [
            'traffic_source' => $this->classify($referrerDomain, $queryParams, $utm, $currentHost),
            'referrer_domain' => $referrerDomain,
            ...$utm,
        ];
    }

    /**
     * @param  array<string, mixed>  $queryParams
     * @param  array<string, ?string>  $utm
     */
    private function classify(?string $referrerDomain, array $queryParams, array $utm, string $currentHost): string
    {
        $medium = $utm['utm_medium'] ? strtolower($utm['utm_medium']) : null;

        foreach (config('analytics.traffic_sources.paid_click_ids', []) as $clickIdParam) {
            if (! empty($queryParams[$clickIdParam])) {
                return 'paid';
            }
        }

        if ($medium && in_array($medium, config('analytics.traffic_sources.paid_mediums', []), true)) {
            return 'paid';
        }

        if ($medium === 'social') {
            return 'social';
        }

        if ($referrerDomain && $this->domainMatches($referrerDomain, config('analytics.traffic_sources.social', []))) {
            return 'social';
        }

        if ($medium === 'organic') {
            return 'organic';
        }

        if ($referrerDomain && $this->domainMatches($referrerDomain, config('analytics.traffic_sources.search_engines', []))) {
            return 'organic';
        }

        if ($referrerDomain && $referrerDomain !== $currentHost) {
            return 'referral';
        }

        return 'direct';
    }

    /**
     * @param  string[]  $knownDomains
     */
    private function domainMatches(string $domain, array $knownDomains): bool
    {
        foreach ($knownDomains as $known) {
            // Brand-prefix match (e.g. "google.co.in" for known "google.com")
            // catches regional TLDs at the cost of a rare false positive on
            // an unrelated domain that happens to start with the same
            // brand label - an accepted v1 simplification.
            $brand = strstr($known, '.', true) ?: $known;

            if ($domain === $known
                || str_ends_with($domain, '.'.$known)
                || $domain === $brand
                || str_starts_with($domain, $brand.'.')) {
                return true;
            }
        }

        return false;
    }

    private function extractDomain(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            return null;
        }

        return preg_replace('/^www\./i', '', strtolower($host));
    }

    /**
     * @return array<string, mixed>
     */
    private function parseQueryString(?string $queryString): array
    {
        if (! $queryString) {
            return [];
        }

        parse_str(ltrim($queryString, '?'), $params);

        return $params;
    }
}
