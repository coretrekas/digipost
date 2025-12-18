<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Http;

use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Exceptions\ApiException;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * HTTP client for communicating with the Digipost API.
 */
final readonly class DigipostHttpClient
{
    private const USER_AGENT = 'digipost-api-client-php/1.0.0';

    private Client $client;

    private RequestSigner $requestSigner;

    public function __construct(
        private DigipostClientConfig $config,
        private SenderId $senderId,
        Signer $signer,
    ) {
        $this->client = new Client([
            'base_uri' => $config->apiUri,
            'timeout' => $config->requestTimeout,
            'connect_timeout' => $config->connectionTimeout,
            'http_errors' => false,
        ]);

        $this->requestSigner = new RequestSigner($signer);
    }

    /**
     * Send a GET request.
     *
     * @param array<string, mixed> $queryParams
     * @param array<string, string> $headers
     *
     * @throws ApiException
     */
    public function get(string $path, array $queryParams = [], array $headers = []): string
    {
        if ($queryParams !== []) {
            $path .= '?'.http_build_query($queryParams);
        }

        $response = $this->request('GET', $path, $headers);

        return (string) $response->getBody();
    }

    /**
     * Send a POST request with XML body.
     *
     * @param array<string, string> $headers
     *
     * @throws ApiException
     */
    public function post(string $path, string $body, array $headers = []): string
    {
        $response = $this->request('POST', $path, $headers, $body);

        return (string) $response->getBody();
    }

    /**
     * Send a POST request with multipart content.
     *
     * @param array<int, array{name: string, contents: string|StreamInterface, headers?: array<string, string>, filename?: string}> $multipart
     * @param array<string, string> $headers
     *
     * @throws ApiException
     */
    public function postMultipart(string $path, array $multipart, array $headers = []): string
    {
        $stream = new MultipartStream($multipart);
        $headers['Content-Type'] = 'multipart/vnd.digipost-v8+xml; boundary='.$stream->getBoundary();

        $response = $this->request('POST', $path, $headers, $stream);

        return (string) $response->getBody();
    }

    /**
     * Send a PUT request.
     *
     * @param array<string, string> $headers
     *
     * @throws ApiException
     */
    public function put(string $path, string $body, array $headers = []): string
    {
        $response = $this->request('PUT', $path, $headers, $body);

        return (string) $response->getBody();
    }

    /**
     * Send a DELETE request.
     *
     * @param array<string, string> $headers
     *
     * @throws ApiException
     */
    public function delete(string $path, array $headers = []): void
    {
        $this->request('DELETE', $path, $headers);
    }

    /**
     * Get a stream response (for downloading content).
     *
     * @param array<string, string> $headers
     *
     * @throws ApiException
     */
    public function getStream(string $path, array $headers = []): StreamInterface
    {
        $response = $this->request('GET', $path, $headers);

        return $response->getBody();
    }

    /**
     * Build the full URL for a path.
     */
    public function buildUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim($this->config->apiUri, '/').'/'.ltrim($path, '/');
    }

    /**
     * Get the sender ID.
     */
    public function getSenderId(): SenderId
    {
        return $this->senderId;
    }

    /**
     * Send an HTTP request.
     *
     * @param array<string, string> $headers
     *
     * @throws ApiException
     */
    private function request(
        string $method,
        string $path,
        array $headers = [],
        string|StreamInterface|null $body = null,
    ): ResponseInterface {
        $url = $this->buildUrl($path);

        $defaultHeaders = $this->getDefaultHeaders();
        $headers = array_merge($defaultHeaders, $headers);

        // Add content type for requests with body
        if ($body !== null && !isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/vnd.digipost-v8+xml';
        }

        // Calculate X-Content-SHA256 header (hash of request body)
        // Only include for requests with body content
        $bodyString = $body instanceof StreamInterface ? (string) $body : ($body ?? '');
        if ($bodyString !== '') {
            $headers['X-Content-SHA256'] = $this->requestSigner->calculateContentHash($bodyString);
        }

        // Sign the request using method, URL, and headers
        $signature = $this->requestSigner->sign($method, $url, $headers);
        $headers['X-Digipost-Signature'] = $signature;

        $request = new Request($method, $url, $headers, $body);

        try {
            $response = $this->client->send($request);
        } catch (GuzzleException $e) {
            throw new ApiException(
                message: 'HTTP request failed: '.$e->getMessage(),
                statusCode: 0,
            );
        }

        $this->checkResponse($response);

        return $response;
    }

    /**
     * Get default headers for all requests.
     *
     * @return array<string, string>
     */
    private function getDefaultHeaders(): array
    {
        return [
            'Accept' => 'application/vnd.digipost-v8+xml',
            'User-Agent' => self::USER_AGENT,
            'X-Digipost-UserId' => (string) $this->senderId,
            'Date' => gmdate('D, d M Y H:i:s T'),
        ];
    }

    /**
     * Check the response for errors.
     *
     * @throws ApiException
     */
    private function checkResponse(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            return;
        }

        throw ApiException::fromResponse($response);
    }
}
