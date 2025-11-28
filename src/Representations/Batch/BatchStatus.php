<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Batch;

/**
 * Status of a batch.
 */
enum BatchStatus: string
{
    case CREATED = 'CREATED';
    case PROCESSING = 'PROCESSING';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
    case FAILED = 'FAILED';
}
