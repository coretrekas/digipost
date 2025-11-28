<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

/**
 * Delivery status of a document.
 */
enum DeliveryStatus: string
{
    case NOT_DELIVERED = 'NOT_DELIVERED';
    case DELIVERED = 'DELIVERED';
}
