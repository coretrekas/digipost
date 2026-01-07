<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Recipients;

use InvalidArgumentException;

/**
 * Norwegian organisation number (organisasjonsnummer).
 */
final readonly class OrganisationNumber
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = preg_replace('/[\s.]/', '', $value) ?? $value;

        if (strlen($normalized) !== 9) {
            throw new InvalidArgumentException('Organisation number must be exactly 9 digits');
        }

        if (!ctype_digit($normalized)) {
            throw new InvalidArgumentException('Organisation number must contain only digits');
        }

        $this->value = $normalized;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
