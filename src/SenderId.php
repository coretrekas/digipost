<?php

declare(strict_types=1);

namespace Coretrek\Digipost;

use InvalidArgumentException;
use Stringable;

/**
 * Represents a sender ID in Digipost.
 *
 * A sender ID is a unique identifier for an organization that can send messages through Digipost.
 */
final readonly class SenderId implements Stringable
{
    private function __construct(
        public int $value,
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException('Sender ID must be a positive integer');
        }
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Create a new SenderId from an integer value.
     */
    public static function of(int $value): self
    {
        return new self($value);
    }

    /**
     * Convert to a BrokerId.
     */
    public function asBrokerId(): BrokerId
    {
        return BrokerId::of($this->value);
    }
}
