<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

/**
 * Result codes for identification requests.
 */
enum IdentificationResultCode: string
{
    /**
     * The recipient is a Digipost user.
     */
    case DIGIPOST = 'DIGIPOST';

    /**
     * The recipient is identified but not a Digipost user.
     */
    case IDENTIFIED = 'IDENTIFIED';

    /**
     * The identification was invalid.
     */
    case INVALID = 'INVALID';

    /**
     * The recipient could not be identified.
     */
    case UNIDENTIFIED = 'UNIDENTIFIED';
}
