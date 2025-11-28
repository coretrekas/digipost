<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Print;

/**
 * How to handle non-deliverable mail.
 */
enum NondeliverableHandling: string
{
    case RETURN_TO_SENDER = 'RETURN_TO_SENDER';
    case SHRED = 'SHRED';
}
