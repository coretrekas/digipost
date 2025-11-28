<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\FileType;

describe('FileType', function (): void {
    it('has correct MIME type for PDF', function (): void {
        expect(FileType::PDF->getMimeType())->toBe('application/pdf');
    });

    it('has correct MIME type for HTML', function (): void {
        expect(FileType::HTML->getMimeType())->toBe('text/html');
    });

    it('has correct MIME type for TXT', function (): void {
        expect(FileType::TXT->getMimeType())->toBe('text/plain');
    });

    it('has correct MIME type for JPG', function (): void {
        expect(FileType::JPG->getMimeType())->toBe('image/jpeg');
    });

    it('has correct MIME type for JPEG', function (): void {
        expect(FileType::JPEG->getMimeType())->toBe('image/jpeg');
    });

    it('has correct MIME type for PNG', function (): void {
        expect(FileType::PNG->getMimeType())->toBe('image/png');
    });

    it('has correct MIME type for GIF', function (): void {
        expect(FileType::GIF->getMimeType())->toBe('image/gif');
    });

    it('can be created from extension', function (): void {
        expect(FileType::fromExtension('pdf'))->toBe(FileType::PDF);
        expect(FileType::fromExtension('.pdf'))->toBe(FileType::PDF);
        expect(FileType::fromExtension('PDF'))->toBe(FileType::PDF);
    });

    it('throws exception for unknown extension', function (): void {
        FileType::fromExtension('xyz');
    })->throws(ValueError::class);
});
