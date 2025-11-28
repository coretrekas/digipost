<?php

declare(strict_types=1);

namespace Coretrek\Digipost;

use InvalidArgumentException;
use Stringable;

/**
 * Represents a broker ID in Digipost.
 *
 * A broker ID is a unique identifier for an organization that can send messages
 * on behalf of other organizations through Digipost.
 */
final readonly class BrokerId implements Stringable
{
    private function __construct(
        public int $value,
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException('Broker ID must be a positive integer');
        }
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Create a new BrokerId from an integer value.
     */
    public static function of(int $value): self
    {
        return new self($value);
    }

    /**
     * Convert to a SenderId.
     */
    public function asSenderId(): SenderId
    {
        return SenderId::of($this->value);
    }
}
