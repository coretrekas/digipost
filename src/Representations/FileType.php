<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

/**
 * Supported file types for documents.
 */
enum FileType: string
{
    case PDF = 'pdf';
    case HTML = 'html';
    case TXT = 'txt';
    case JPG = 'jpg';
    case JPEG = 'jpeg';
    case PNG = 'png';
    case GIF = 'gif';

    /**
     * Get the MIME type for this file type.
     */
    public function getMimeType(): string
    {
        return match ($this) {
            self::PDF => 'application/pdf',
            self::HTML => 'text/html',
            self::TXT => 'text/plain',
            self::JPG, self::JPEG => 'image/jpeg',
            self::PNG => 'image/png',
            self::GIF => 'image/gif',
        };
    }

    /**
     * Create from a file extension.
     */
    public static function fromExtension(string $extension): self
    {
        $extension = strtolower(ltrim($extension, '.'));

        return self::from($extension);
    }
}
