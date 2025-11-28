<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

/**
 * Types of document events.
 */
enum DocumentEventType: string
{
    case OPENED = 'OPENED';
    case OPENED_ATTACHMENT = 'OPENED_ATTACHMENT';
    case PRINT_FAILED = 'PRINT_FAILED';
    case POSTMARKED = 'POSTMARKED';
    case SHREDDED = 'SHREDDED';
    case MOVED_TO_INBOX = 'MOVED_TO_INBOX';
    case SHARE_DOCUMENTS_REQUEST_DOCUMENTS_SHARED = 'SHARE_DOCUMENTS_REQUEST_DOCUMENTS_SHARED';
    case SHARE_DOCUMENTS_REQUEST_CANCELLED = 'SHARE_DOCUMENTS_REQUEST_CANCELLED';
    case EMAIL_NOTIFICATION_FAILED = 'EMAIL_NOTIFICATION_FAILED';
}
