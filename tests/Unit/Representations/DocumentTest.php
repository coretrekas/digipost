<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\AuthenticationLevel;
use Coretrek\Digipost\Representations\Document;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Representations\SensitivityLevel;
use Ramsey\Uuid\Uuid;

describe('Document', function (): void {
    it('can be created with required fields', function (): void {
        $uuid = Uuid::uuid4();
        $document = new Document(
            uuid: $uuid,
            subject: 'Test Document',
            fileType: FileType::PDF,
        );

        expect($document->uuid)->toBe($uuid);
        expect($document->subject)->toBe('Test Document');
        expect($document->fileType)->toBe(FileType::PDF);
    });

    it('has default authentication level', function (): void {
        $uuid = Uuid::uuid4();
        $document = new Document(
            uuid: $uuid,
            subject: 'Test Document',
            fileType: FileType::PDF,
        );

        expect($document->authenticationLevel)->toBe(AuthenticationLevel::PASSWORD);
    });

    it('has default sensitivity level', function (): void {
        $uuid = Uuid::uuid4();
        $document = new Document(
            uuid: $uuid,
            subject: 'Test Document',
            fileType: FileType::PDF,
        );

        expect($document->sensitivityLevel)->toBe(SensitivityLevel::NORMAL);
    });

    it('can have custom authentication level', function (): void {
        $uuid = Uuid::uuid4();
        $document = new Document(
            uuid: $uuid,
            subject: 'Test Document',
            fileType: FileType::PDF,
            authenticationLevel: AuthenticationLevel::TWO_FACTOR,
        );

        expect($document->authenticationLevel)->toBe(AuthenticationLevel::TWO_FACTOR);
    });

    it('can have custom sensitivity level', function (): void {
        $uuid = Uuid::uuid4();
        $document = new Document(
            uuid: $uuid,
            subject: 'Test Document',
            fileType: FileType::PDF,
            sensitivityLevel: SensitivityLevel::SENSITIVE,
        );

        expect($document->sensitivityLevel)->toBe(SensitivityLevel::SENSITIVE);
    });
});
