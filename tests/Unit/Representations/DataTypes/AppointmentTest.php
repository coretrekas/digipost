<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\DataTypes\Appointment;
use Coretrek\Digipost\Representations\DataTypes\AppointmentAddress;
use Coretrek\Digipost\Representations\DataTypes\Info;
use Coretrek\Digipost\Representations\DataTypes\Language;

describe('Appointment', function (): void {
    it('can be created with required fields', function (): void {
        $startTime = new DateTimeImmutable('2024-12-15 10:00:00');

        $appointment = new Appointment(
            startTime: $startTime,
        );

        expect($appointment->startTime)->toBe($startTime);
        expect($appointment->language)->toBe(Language::NO);
    });

    it('can have end time', function (): void {
        $startTime = new DateTimeImmutable('2024-12-15 10:00:00');
        $endTime = new DateTimeImmutable('2024-12-15 11:00:00');

        $appointment = new Appointment(
            startTime: $startTime,
            endTime: $endTime,
        );

        expect($appointment->endTime)->toBe($endTime);
    });

    it('can have address', function (): void {
        $startTime = new DateTimeImmutable('2024-12-15 10:00:00');
        $address = new AppointmentAddress(
            streetAddress: 'Testgata 1',
            postalCode: '0123',
            city: 'Oslo',
        );

        $appointment = new Appointment(
            startTime: $startTime,
            address: $address,
        );

        expect($appointment->address)->toBe($address);
    });

    it('can have info items', function (): void {
        $startTime = new DateTimeImmutable('2024-12-15 10:00:00');
        $info = [
            new Info(title: 'Bring', text: 'ID card'),
            new Info(title: 'Note', text: 'Arrive 10 minutes early'),
        ];

        $appointment = new Appointment(
            startTime: $startTime,
            info: $info,
        );

        expect($appointment->info)->toHaveCount(2);
    });

    it('can have custom language', function (): void {
        $startTime = new DateTimeImmutable('2024-12-15 10:00:00');

        $appointment = new Appointment(
            startTime: $startTime,
            language: Language::EN,
        );

        expect($appointment->language)->toBe(Language::EN);
    });

    it('can generate XML', function (): void {
        $startTime = new DateTimeImmutable('2024-12-15 10:00:00');

        $appointment = new Appointment(
            startTime: $startTime,
            arrivalInfo: 'Main entrance',
            place: 'Oslo Hospital',
        );

        $xml = new SimpleXMLElement('<document></document>');
        $appointment->addToXml($xml);

        $xmlString = $xml->asXML();

        expect($xmlString)->toContain('appointment');
        expect($xmlString)->toContain('Oslo Hospital');
        expect($xmlString)->toContain('Main entrance');
    });
});
