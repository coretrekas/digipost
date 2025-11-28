<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

/**
 * Status of a sender.
 */
enum SenderStatus: string
{
    case VALID_SENDER = 'VALID_SENDER';
    case NO_BILLING_AGREEMENT = 'NO_BILLING_AGREEMENT';
    case UNKNOWN_SENDER = 'UNKNOWN_SENDER';
}
