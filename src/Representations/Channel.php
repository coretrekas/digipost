<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

/**
 * Delivery channel for a document.
 */
enum Channel: string
{
    case DIGIPOST = 'DIGIPOST';
    case PRINT = 'PRINT';
}
