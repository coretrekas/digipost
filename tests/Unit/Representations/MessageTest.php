<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\DigipostAddress;
use Coretrek\Digipost\Representations\Document;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\SenderId;
use Ramsey\Uuid\Uuid;

describe('Message', function (): void {
    it('can be created with builder', function (): void {
        $uuid = Uuid::uuid4();
        $document = new Document(
            uuid: $uuid,
            subject: 'Test Document',
            fileType: FileType::PDF,
        );

        $message = Message::newMessage('msg-123', $document)
            ->digipostAddress(new DigipostAddress('john.doe'))
            ->build();

        expect($message->messageId)->toBe('msg-123');
        expect($message->primaryDocument)->toBe($document);
    });

    it('can have attachments', function (): void {
        $primaryUuid = Uuid::uuid4();
        $attachmentUuid = Uuid::uuid4();

        $primaryDocument = new Document(
            uuid: $primaryUuid,
            subject: 'Primary Document',
            fileType: FileType::PDF,
        );

        $attachment = new Document(
            uuid: $attachmentUuid,
            subject: 'Attachment',
            fileType: FileType::PDF,
        );

        $message = Message::newMessage('msg-123', $primaryDocument)
            ->digipostAddress(new DigipostAddress('john.doe'))
            ->attachments($attachment)
            ->build();

        expect($message->attachments)->toHaveCount(1);
        expect($message->attachments[0])->toBe($attachment);
    });

    it('can have sender ID', function (): void {
        $uuid = Uuid::uuid4();
        $document = new Document(
            uuid: $uuid,
            subject: 'Test Document',
            fileType: FileType::PDF,
        );

        $senderId = SenderId::of(123456);

        $message = Message::newMessage('msg-123', $document)
            ->digipostAddress(new DigipostAddress('john.doe'))
            ->senderId($senderId)
            ->build();

        expect($message->senderId)->toBe($senderId);
    });

    it('can generate XML', function (): void {
        $uuid = Uuid::uuid4();
        $document = new Document(
            uuid: $uuid,
            subject: 'Test Document',
            fileType: FileType::PDF,
        );

        $message = Message::newMessage('msg-123', $document)
            ->digipostAddress(new DigipostAddress('john.doe'))
            ->build();

        $xml = $message->toXml();

        expect($xml)->toContain('msg-123');
        expect($xml)->toContain('Test Document');
        expect($xml)->toContain('john.doe');
    });
});
