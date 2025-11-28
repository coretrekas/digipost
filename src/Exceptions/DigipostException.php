<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Exceptions;

use Exception;

/**
 * Base exception for all Digipost client exceptions.
 */
class DigipostException extends Exception
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?Exception $previous = null,
        public readonly array $context = [],
    ) {
        parent::__construct($message, $code, $previous);
    }
}
