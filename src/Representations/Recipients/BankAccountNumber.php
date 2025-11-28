<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Recipients;

use InvalidArgumentException;
use Stringable;

/**
 * Norwegian bank account number.
 */
final readonly class BankAccountNumber implements Stringable
{
    public function __construct(
        public string $value,
    ) {
        if (!self::isValid($value)) {
            throw new InvalidArgumentException('Invalid bank account number');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Validate a Norwegian bank account number.
     */
    public static function isValid(string $value): bool
    {
        // Remove any dots or spaces
        $cleanedValue = preg_replace('/[\s.]/', '', $value);

        // Must be exactly 11 digits
        if ($cleanedValue === null || preg_match('/^\d{11}$/', $cleanedValue) !== 1) {
            return false;
        }

        $value = $cleanedValue;

        // Validate checksum using MOD11
        $weights = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $digits = array_map(intval(...), str_split($value));

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $digits[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $checkDigit = $remainder === 0 ? 0 : 11 - $remainder;

        // If checkDigit is 10, the account number is invalid
        if ($checkDigit === 10) {
            return false;
        }

        return $checkDigit === $digits[10];
    }
}
