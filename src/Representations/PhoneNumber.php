<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use InvalidArgumentException;
use Stringable;

/**
 * A phone number for SMS notifications.
 */
final readonly class PhoneNumber implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = self::normalize($value);

        if (!self::isValid($this->value)) {
            throw new InvalidArgumentException('Invalid phone number');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Normalize a phone number.
     *
     * If the number doesn't start with +, 00, or 011, and is 8 digits,
     * prepend +47 (Norwegian country code).
     */
    public static function normalize(string $value): string
    {
        // Remove whitespace
        $value = preg_replace('/\s+/', '', $value) ?? $value;

        // If it starts with +, 00, or 011, it's already international
        if (str_starts_with($value, '+') || str_starts_with($value, '00') || str_starts_with($value, '011')) {
            return $value;
        }

        // If it's exactly 8 digits, assume Norwegian number
        if (preg_match('/^\d{8}$/', $value) === 1) {
            return '+47'.$value;
        }

        return $value;
    }

    /**
     * Validate a phone number.
     */
    public static function isValid(string $value): bool
    {
        // Must start with + and contain only digits after
        if (!str_starts_with($value, '+')) {
            return false;
        }

        $digits = substr($value, 1);

        // Must be between 8 and 15 digits
        return preg_match('/^\d{8,15}$/', $digits) === 1;
    }
}
