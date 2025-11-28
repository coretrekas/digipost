<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

/**
 * Authentication level required to open a document.
 */
enum AuthenticationLevel: string
{
    /**
     * Password authentication (default).
     */
    case PASSWORD = 'PASSWORD';

    /**
     * Two-factor authentication (BankID or BuyPass).
     */
    case TWO_FACTOR = 'TWO_FACTOR';

    /**
     * ID-porten level 3.
     */
    case IDPORTEN_3 = 'IDPORTEN_3';

    /**
     * ID-porten level 4.
     */
    case IDPORTEN_4 = 'IDPORTEN_4';
}
