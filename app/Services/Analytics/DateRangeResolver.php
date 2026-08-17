<?php

namespace App\Services\Analytics;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class DateRangeResolver
{
    private const PRESETS = ['today', 'yesterday', '7d', '30d', 'this_month', 'last_month', 'custom'];

    public function resolve(?string $preset, ?string $from = null, ?string $to = null): CarbonPeriod
    {
        $preset = in_array($preset, self::PRESETS, true) ? $preset : '7d';

        [$start, $end] = match ($preset) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            '7d' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            '30d' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'this_month' => [now()->startOfMonth(), now()->endOfDay()],
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'custom' => $this->customRange($from, $to),
        };

        return CarbonPeriod::create($start, $end);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function customRange(?string $from, ?string $to): array
    {
        $start = $from ? Carbon::parse($from)->startOfDay() : now()->subDays(6)->startOfDay();
        $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }
}
