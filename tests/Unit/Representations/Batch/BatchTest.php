<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\Batch\Batch;
use Coretrek\Digipost\Representations\Batch\BatchStatus;
use Ramsey\Uuid\Uuid;

describe('Batch', function (): void {
    it('can be created', function (): void {
        $batch = Batch::create();

        expect($batch->uuid)->not->toBeNull();
        expect($batch->status)->toBe(BatchStatus::CREATED);
    });

    it('can generate XML', function (): void {
        $batch = Batch::create();
        $xml = $batch->toXml();

        expect($xml)->toContain('batch');
        expect($xml)->toContain($batch->uuid->toString());
    });

    it('can parse from XML', function (): void {
        $uuid = Uuid::uuid4();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <batch xmlns="http://api.digipost.no/schema/v8">
                <uuid>'.$uuid->toString().'</uuid>
                <status>PROCESSING</status>
                <message-count>10</message-count>
                <completed-count>5</completed-count>
                <failed-count>1</failed-count>
            </batch>';

        $batch = Batch::fromXml($xml);

        expect($batch->uuid->toString())->toBe($uuid->toString());
        expect($batch->status)->toBe(BatchStatus::PROCESSING);
        expect($batch->messageCount)->toBe(10);
        expect($batch->completedCount)->toBe(5);
        expect($batch->failedCount)->toBe(1);
    });
});
