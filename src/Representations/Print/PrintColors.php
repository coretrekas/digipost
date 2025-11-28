<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Print;

/**
 * Print color options.
 */
enum PrintColors: string
{
    case MONOCHROME = 'MONOCHROME';
    case COLORS = 'COLORS';
}
