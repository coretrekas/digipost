<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use InvalidArgumentException;
use Stringable;

/**
 * A Digipost address (username).
 */
final readonly class DigipostAddress implements Stringable
{
    public function __construct(
        public string $value,
    ) {
        if (!self::isValid($value)) {
            throw new InvalidArgumentException('Invalid Digipost address');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Validate a Digipost address.
     */
    public static function isValid(string $value): bool
    {
        // Digipost addresses are alphanumeric with dots and hyphens
        // They must be between 2 and 100 characters
        $value = trim($value);

        if (strlen($value) < 2 || strlen($value) > 100) {
            return false;
        }

        return (bool) preg_match('/^[a-zA-Z0-9][a-zA-Z0-9.\-#]*[a-zA-Z0-9]$/', $value);
    }
}
