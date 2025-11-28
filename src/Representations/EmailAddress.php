<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use InvalidArgumentException;
use Stringable;

/**
 * An email address.
 */
final readonly class EmailAddress implements Stringable
{
    public function __construct(
        public string $value,
    ) {
        if (!self::isValid($value)) {
            throw new InvalidArgumentException('Invalid email address');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Validate an email address.
     */
    public static function isValid(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
