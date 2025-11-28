<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

/**
 * Sensitivity level for a document.
 */
enum SensitivityLevel: string
{
    /**
     * Normal sensitivity - sender and subject visible before login.
     */
    case NORMAL = 'NORMAL';

    /**
     * Sensitive - sender and subject hidden until logged in at appropriate level.
     */
    case SENSITIVE = 'SENSITIVE';
}
