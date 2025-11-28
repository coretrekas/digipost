<?php

declare(strict_types=1);

use Coretrek\Digipost\Exceptions\ApiException;

describe('ApiException', function (): void {
    it('can be created with message and status code', function (): void {
        $exception = new ApiException(
            message: 'Something went wrong',
            statusCode: 400,
        );

        expect($exception->getMessage())->toBe('Something went wrong');
        expect($exception->statusCode)->toBe(400);
    });

    it('can have error code', function (): void {
        $exception = new ApiException(
            message: 'Something went wrong',
            statusCode: 400,
            errorCode: 'INVALID_REQUEST',
        );

        expect($exception->errorCode)->toBe('INVALID_REQUEST');
    });

    it('can have error type', function (): void {
        $exception = new ApiException(
            message: 'Something went wrong',
            statusCode: 400,
            errorType: 'CLIENT_ERROR',
        );

        expect($exception->errorType)->toBe('CLIENT_ERROR');
    });
});
