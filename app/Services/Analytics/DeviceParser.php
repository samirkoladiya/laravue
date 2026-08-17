<?php

namespace App\Services\Analytics;

/**
 * Hand-rolled, deliberately small User-Agent parser. Not jenssegers/agent
 * (archived/unmaintained, pulls in a heavy transitive dependency for more
 * than this needs) - just device type + the handful of browsers/OSes that
 * make up the overwhelming majority of real traffic. Expect to iterate on
 * this once real UAs are seen.
 */
class DeviceParser
{
    /**
     * @return array{device_type: string, browser: ?string, browser_version: ?string, os: ?string, os_version: ?string}
     */
    public function parse(?string $userAgent): array
    {
        $ua = $userAgent ?? '';

        $browser = $this->detectBrowser($ua);
        $os = $this->detectOs($ua);

        return [
            'device_type' => $this->detectDeviceType($ua),
            'browser' => $browser,
            'browser_version' => $this->detectBrowserVersion($ua, $browser),
            'os' => $os,
            'os_version' => $this->detectOsVersion($ua, $os),
        ];
    }

    private function detectDeviceType(string $ua): string
    {
        if (preg_match('/iPad|Tablet(?!.*Mobile)/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/Mobile|Android|iPhone|iPod|Windows Phone/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function detectBrowser(string $ua): ?string
    {
        // Order matters: Edge/Opera/Samsung Internet UAs also contain
        // "Chrome" and "Safari" tokens, so the more specific check must
        // run first.
        return match (true) {
            (bool) preg_match('/Edg\//i', $ua) => 'Edge',
            (bool) preg_match('/OPR\/|Opera/i', $ua) => 'Opera',
            (bool) preg_match('/SamsungBrowser\//i', $ua) => 'Samsung Internet',
            (bool) preg_match('/Firefox\//i', $ua) => 'Firefox',
            (bool) preg_match('/Chrome\//i', $ua) => 'Chrome',
            (bool) preg_match('/Version\/.*Safari\//i', $ua) => 'Safari',
            (bool) preg_match('/MSIE |Trident\//i', $ua) => 'Internet Explorer',
            default => null,
        };
    }

    private function detectBrowserVersion(string $ua, ?string $browser): ?string
    {
        $pattern = match ($browser) {
            'Edge' => '/Edg\/([\d.]+)/i',
            'Opera' => '/(?:OPR|Opera)[\/ ]([\d.]+)/i',
            'Samsung Internet' => '/SamsungBrowser\/([\d.]+)/i',
            'Firefox' => '/Firefox\/([\d.]+)/i',
            'Chrome' => '/Chrome\/([\d.]+)/i',
            'Safari' => '/Version\/([\d.]+)/i',
            'Internet Explorer' => '/(?:MSIE |rv:)([\d.]+)/i',
            default => null,
        };

        if ($pattern === null || ! preg_match($pattern, $ua, $matches)) {
            return null;
        }

        return $this->majorMinor($matches[1]);
    }

    private function detectOs(string $ua): ?string
    {
        // iOS UAs also contain "like Mac OS X" (a compatibility token), so
        // the iOS check must run before the macOS one or every iPhone/iPad
        // gets misclassified as a Mac.
        return match (true) {
            (bool) preg_match('/Windows/i', $ua) => 'Windows',
            (bool) preg_match('/iPhone|iPad|iPod/i', $ua) => 'iOS',
            (bool) preg_match('/Mac OS X/i', $ua) => 'macOS',
            (bool) preg_match('/Android/i', $ua) => 'Android',
            (bool) preg_match('/Linux/i', $ua) => 'Linux',
            default => null,
        };
    }

    private function detectOsVersion(string $ua, ?string $os): ?string
    {
        $pattern = match ($os) {
            'Windows' => '/Windows NT ([\d.]+)/i',
            'macOS' => '/Mac OS X ([\d_.]+)/i',
            'Android' => '/Android ([\d.]+)/i',
            'iOS' => '/OS ([\d_]+) like Mac OS X/i',
            default => null,
        };

        if ($pattern === null || ! preg_match($pattern, $ua, $matches)) {
            return null;
        }

        return $this->majorMinor(str_replace('_', '.', $matches[1]));
    }

    private function majorMinor(string $version): string
    {
        return implode('.', array_slice(explode('.', $version), 0, 2));
    }
}
