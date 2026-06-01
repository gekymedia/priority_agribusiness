<?php

namespace App\Support;

class PhoneNormalizer
{
    /**
     * Normalize a Ghana phone number to 233XXXXXXXXX format.
     */
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '233')) {
            return $digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '233' . substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Phone variants to match against stored numbers.
     *
     * @return array<int, string>
     */
    public static function variants(?string $phone): array
    {
        $normalized = self::normalize($phone);
        if (! $normalized) {
            return [];
        }

        $variants = [$normalized];

        if (str_starts_with($normalized, '233') && strlen($normalized) === 12) {
            $local = '0' . substr($normalized, 3);
            $variants[] = $local;
            $variants[] = '+' . $normalized;
        }

        return array_values(array_unique($variants));
    }

    /**
     * Check if an incoming phone matches any stored phone value(s).
     */
    public static function matchesAny(string $incoming, ?string ...$storedPhones): bool
    {
        $incomingVariants = self::variants($incoming);
        if ($incomingVariants === []) {
            return false;
        }

        foreach ($storedPhones as $stored) {
            if ($stored === null || trim($stored) === '') {
                continue;
            }

            $storedVariants = self::variants($stored);
            if ($storedVariants !== [] && array_intersect($incomingVariants, $storedVariants) !== []) {
                return true;
            }
        }

        return false;
    }
}
