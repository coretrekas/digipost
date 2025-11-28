<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

/**
 * Exception thrown when the API returns an error response.
 */
final class ApiException extends DigipostException
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly ?string $errorCode = null,
        public readonly ?string $errorType = null,
        public readonly ?ResponseInterface $response = null,
    ) {
        parent::__construct(
            message: $message,
            code: $statusCode,
            context: [
                'status_code' => $statusCode,
                'error_code' => $errorCode,
                'error_type' => $errorType,
            ],
        );
    }

    public static function fromResponse(ResponseInterface $response): self
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        $errorCode = null;
        $errorType = null;
        $message = "API request failed with status code {$statusCode}";

        // Try to parse XML error response
        if (str_contains($response->getHeaderLine('Content-Type'), 'xml')) {
            try {
                $xml = new SimpleXMLElement($body);
                $errorCode = (string) ($xml->{'error-code'} ?? '');
                $errorType = (string) ($xml->{'error-type'} ?? '');
                $errorMessage = (string) ($xml->{'error-message'} ?? '');

                if ($errorMessage !== '') {
                    $message = $errorMessage;
                }
            } catch (Exception) {
                // Ignore XML parsing errors
            }
        }

        return new self(
            message: $message,
            statusCode: $statusCode,
            errorCode: $errorCode !== '' ? $errorCode : null,
            errorType: $errorType !== '' ? $errorType : null,
            response: $response,
        );
    }

    public static function notFound(string $resource): self
    {
        return new self(
            message: "Resource not found: {$resource}",
            statusCode: 404,
            errorCode: 'NOT_FOUND',
        );
    }

    public static function unauthorized(): self
    {
        return new self(
            message: 'Unauthorized: Invalid or missing authentication',
            statusCode: 401,
            errorCode: 'UNAUTHORIZED',
        );
    }

    public static function forbidden(string $reason = ''): self
    {
        $message = 'Forbidden: Access denied';
        if ($reason !== '') {
            $message .= ": {$reason}";
        }

        return new self(
            message: $message,
            statusCode: 403,
            errorCode: 'FORBIDDEN',
        );
    }

    public static function badRequest(string $reason): self
    {
        return new self(
            message: "Bad request: {$reason}",
            statusCode: 400,
            errorCode: 'BAD_REQUEST',
        );
    }

    public static function serverError(string $message = 'Internal server error'): self
    {
        return new self(
            message: $message,
            statusCode: 500,
            errorCode: 'SERVER_ERROR',
        );
    }
}
