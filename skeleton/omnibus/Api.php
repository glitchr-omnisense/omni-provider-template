<?php

namespace __Namespace__;

use Omnibus\Exception\CarrierException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__'s HTTP API: every call goes through request(), where its failures
 * become the family's CarrierException - an HTTP status of 400 or more, no
 * answer, a body that is not JSON (a proxy's page) - with the carrier's own
 * error code when it gives one. Replace the authentication, the address and
 * the error's fields with the service's.
 */
final class Api
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $carrier,
        #[\SensitiveParameter] private readonly string $apiKey,
        private readonly string $baseUri,
    ) {
    }

    /**
     * @param array<string, mixed> $options the HTTP client's options (query, json, body...)
     *
     * @return array<mixed> the answer's JSON
     */
    public function request(string $method, string $path, array $options = []): array
    {
        try {
            $response = $this->http->request($method, rtrim($this->baseUri, '/').'/'.ltrim($path, '/'), $options + [
                'headers' => ['Authorization' => 'Bearer '.$this->apiKey, 'Accept' => 'application/json'],
                'timeout' => 15,
            ]);
            $status = $response->getStatusCode();
            $body = $response->getContent(false);
        } catch (ExceptionInterface $e) {
            throw new CarrierException($this->carrier, 'No answer: '.$e->getMessage(), null, $e);
        }

        $data = json_decode($body, true);
        if ($status >= 400) {
            $error = \is_array($data) ? $data : [];
            $code = \is_scalar($error['error'] ?? null) ? (string) $error['error'] : (string) $status;
            $message = \is_scalar($error['message'] ?? null) ? (string) $error['message'] : substr(trim($body), 0, 200);

            throw new CarrierException($this->carrier, \sprintf('HTTP %d: %s', $status, $message), $code);
        }
        if (!\is_array($data)) {
            throw new CarrierException($this->carrier, \sprintf('HTTP %d, not JSON: %s', $status, substr(trim($body), 0, 200)));
        }

        return $data;
    }
}
