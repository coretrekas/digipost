<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use InvalidArgumentException;
use Stringable;

/**
 * Norwegian personal identification number (fødselsnummer).
 */
final readonly class PersonalIdentificationNumber implements Stringable
{
    public function __construct(
        public string $value,
    ) {
        if (!self::isValid($value)) {
            throw new InvalidArgumentException('Invalid personal identification number');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Validate a personal identification number.
     */
    public static function isValid(string $value): bool
    {
        // Remove any whitespace
        $cleanedValue = preg_replace('/\s+/', '', $value);

        // Must be exactly 11 digits
        if ($cleanedValue === null || preg_match('/^\d{11}$/', $cleanedValue) !== 1) {
            return false;
        }

        $value = $cleanedValue;

        // Validate checksum (Norwegian fødselsnummer algorithm)
        $weights1 = [3, 7, 6, 1, 8, 9, 4, 5, 2];
        $weights2 = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];

        $digits = array_map(intval(...), str_split($value));

        // Calculate first control digit
        $sum1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum1 += $digits[$i] * $weights1[$i];
        }
        $control1 = 11 - ($sum1 % 11);
        if ($control1 === 11) {
            $control1 = 0;
        }
        if ($control1 === 10 || $control1 !== $digits[9]) {
            return false;
        }

        // Calculate second control digit
        $sum2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum2 += $digits[$i] * $weights2[$i];
        }
        $control2 = 11 - ($sum2 % 11);
        if ($control2 === 11) {
            $control2 = 0;
        }

        return $control2 !== 10 && $control2 === $digits[10];
    }
}
