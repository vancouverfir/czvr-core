<?php

namespace App\Support\Publications;

use Carbon\CarbonImmutable;
use DateTimeInterface;

final class BcVfrTerminalProcedures
{
    private const FIRST_EFFECTIVE_DATE = '2025-10-02';

    private const CYCLE_DAYS = 56;

    private const URL_PREFIX = 'https://imageserver.fltplan.com/legends/BC_VFR_TERMINAL_PROCEDURES_';

    public static function currentUrl(?DateTimeInterface $onDate = null): string
    {
        return self::URL_PREFIX.self::effectiveDate($onDate)->format('m-d-Y').'.PDF';
    }

    public static function resolveUrl(string $url, ?DateTimeInterface $onDate = null): string
    {
        if (! self::manages($url)) {
            return $url;
        }

        return self::currentUrl($onDate);
    }

    public static function resolveText(string $text, string $url, ?DateTimeInterface $onDate = null): string
    {
        if (! self::manages($url)) {
            return $text;
        }

        $effectiveDate = self::effectiveDate($onDate);
        $text = preg_replace(
            '/\b\d{1,2}[-\/]\d{1,2}[-\/]\d{4}\b/',
            $effectiveDate->format('m-d-Y'),
            $text,
        ) ?? $text;

        return preg_replace('/\b20\d{2}\b/', $effectiveDate->format('Y'), $text) ?? $text;
    }

    public static function effectiveDate(?DateTimeInterface $onDate = null): CarbonImmutable
    {
        $today = $onDate
            ? CarbonImmutable::instance($onDate)->startOfDay()
            : CarbonImmutable::today('UTC');
        $effectiveDate = CarbonImmutable::parse(self::FIRST_EFFECTIVE_DATE, 'UTC');

        while ($effectiveDate->addDays(self::CYCLE_DAYS)->lessThanOrEqualTo($today)) {
            $effectiveDate = $effectiveDate->addDays(self::CYCLE_DAYS);
        }

        return $effectiveDate;
    }

    private static function manages(string $url): bool
    {
        return (bool) preg_match('/BC_VFR_TERMINAL_PROCEDURES_\d{2}-\d{2}-\d{4}\.PDF(?:[?#].*)?$/i', $url);
    }
}
