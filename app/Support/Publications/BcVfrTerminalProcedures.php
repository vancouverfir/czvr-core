<?php

namespace App\Support\Publications;

use Carbon\CarbonImmutable;
use DateTimeInterface;

final class BcVfrTerminalProcedures
{
    private const CYCLE_DAYS = 56;

    private const URL_PATTERN = '/BC_VFR_TERMINAL_PROCEDURES_(\d{2}-\d{2}-\d{4})\.PDF(?:[?#].*)?$/i';

    public static function resolveUrl(string $url, ?DateTimeInterface $onDate = null): string
    {
        $effectiveDate = self::effectiveDate($url, $onDate);

        if (! $effectiveDate) {
            return $url;
        }

        return preg_replace_callback(self::URL_PATTERN, fn (array $match): string => str_replace($match[1], $effectiveDate->format('m-d-Y'), $match[0]), $url) ?? $url;
    }

    public static function resolveText(string $text, string $url, ?DateTimeInterface $onDate = null): string
    {
        $effectiveDate = self::effectiveDate($url, $onDate);

        if (! $effectiveDate) {
            return $text;
        }

        $text = preg_replace(
            '/\b\d{1,2}[-\/]\d{1,2}[-\/]\d{4}\b/',
            $effectiveDate->format('m-d-Y'),
            $text,
        ) ?? $text;

        return preg_replace('/\b20\d{2}\b/', $effectiveDate->format('Y'), $text) ?? $text;
    }

    private static function effectiveDate(string $url, ?DateTimeInterface $onDate): ?CarbonImmutable
    {
        if (! preg_match(self::URL_PATTERN, $url, $match)) {
            return null;
        }

        $effectiveDate = CarbonImmutable::createFromFormat('!m-d-Y', $match[1], 'UTC');

        if (! $effectiveDate) {
            return null;
        }

        $today = $onDate
            ? CarbonImmutable::instance($onDate)->startOfDay()
            : CarbonImmutable::today('UTC');

        while ($effectiveDate->addDays(self::CYCLE_DAYS)->lessThanOrEqualTo($today)) {
            $effectiveDate = $effectiveDate->addDays(self::CYCLE_DAYS);
        }

        return $effectiveDate;
    }
}
