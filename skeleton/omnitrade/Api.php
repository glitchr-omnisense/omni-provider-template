<?php

namespace __Namespace__;

use Omnitrade\Exception\ProviderException;
use Omnitrade\Model\Money;
use Omnitrade\Model\Status;
use Omnitrade\Model\Transaction;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__'s HTTP API: every call goes through request(), where its failures
 * become the family's ProviderException - an HTTP status of 400 or more, no
 * answer, a body that is not JSON (a proxy's page) - with the provider's own
 * error code when it gives one; transaction() reads a payment as the family's
 * Transaction. Replace the authentication, the address, the fields and
 * STATUSES with the service's.
 */
final class Api
{
    /** The provider's statuses, in the family's words; any other is an answer it cannot read. */
    private const STATUSES = [
        'open' => Status::PENDING,
        'authorized' => Status::AUTHORIZED,
        'paid' => Status::PAID,
        'failed' => Status::REFUSED,
        'canceled' => Status::CANCELLED,
        'expired' => Status::EXPIRED,
        'partially_refunded' => Status::PARTIALLY_REFUNDED,
        'refunded' => Status::REFUNDED,
    ];

    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $provider,
        #[\SensitiveParameter] private readonly string $secretKey,
        private readonly string $baseUri,
    ) {
    }

    /**
     * @param array<string, mixed> $options the HTTP client's options (query, json, body, headers...)
     *
     * @return array<mixed> the answer's JSON
     */
    public function request(string $method, string $path, array $options = []): array
    {
        $options['headers'] = (\is_array($options['headers'] ?? null) ? $options['headers'] : []) + ['Authorization' => 'Bearer '.$this->secretKey, 'Accept' => 'application/json'];
        try {
            $response = $this->http->request($method, rtrim($this->baseUri, '/').'/'.ltrim($path, '/'), $options + ['timeout' => 15]);
            $status = $response->getStatusCode();
            $body = $response->getContent(false);
        } catch (ExceptionInterface $e) {
            throw new ProviderException($this->provider, 'No answer: '.$e->getMessage(), null, $e);
        }

        $data = json_decode($body, true);
        if ($status >= 400) {
            $error = \is_array($data) ? $data : [];
            $code = \is_scalar($error['error'] ?? null) ? (string) $error['error'] : (string) $status;
            $message = \is_scalar($error['message'] ?? null) ? (string) $error['message'] : substr(trim($body), 0, 200);

            throw new ProviderException($this->provider, \sprintf('HTTP %d: %s', $status, $message), $code);
        }
        if (!\is_array($data)) {
            throw new ProviderException($this->provider, \sprintf('HTTP %d, not JSON: %s', $status, substr(trim($body), 0, 200)));
        }

        return $data;
    }

    /**
     * A payment as the provider answers it:
     * {"id", "status", "amount" (minor units), "currency", "checkout_url"}.
     *
     * @param array<mixed> $payment
     */
    public function transaction(array $payment): Transaction
    {
        $id = $payment['id'] ?? null;
        $status = self::STATUSES[\is_string($payment['status'] ?? null) ? $payment['status'] : ''] ?? null;
        if (!\is_string($id) || '' === $id || null === $status) {
            throw new ProviderException($this->provider, 'A payment without its id or a known status.');
        }
        $amount = $payment['amount'] ?? null;
        $currency = $payment['currency'] ?? null;
        $url = $payment['checkout_url'] ?? null;

        return new Transaction(
            provider: $this->provider,
            reference: $id,
            status: $status,
            amount: \is_int($amount) && \is_string($currency) ? Money::of($amount, strtoupper($currency)) : null,
            redirectUrl: Status::PENDING === $status && \is_string($url) && '' !== $url ? $url : null,
            raw: $payment,
        );
    }
}
