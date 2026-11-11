<?php

namespace __Namespace__;

use Omnisense\Contracts\Policy\Backoff;
use Omnitrade\Exception\ProviderException;
use Omnitrade\Http\Throttle;
use Omnitrade\Model\Money;
use Omnitrade\Model\Status;
use Omnitrade\Model\Transaction;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__'s HTTP API: every call goes through request(), where its failures
 * become the family's ProviderException - an HTTP status of 400 or more, no
 * answer, a body that is not JSON (a proxy's page) - with the provider's own
 * error code when it gives one; its answers are read through Data, so that
 * nothing casts a mixed value blindly; transaction() reads a payment as the
 * family's Transaction. Replace the authentication, the address, the fields and
 * STATUSES with the service's. Given the gateway's Backoff (the factory's
 * Config::backoff()), a 429 is a RateLimitedException and the provider's
 * Retry-After holds every next call, refused without a request.
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
        /** The waits the provider asks for, shared by the gateway's calls (omnisense/contracts' Backoff). */
        private readonly ?Backoff $backoff = null,
    ) {
    }

    /**
     * The answer's JSON, read through Data (never a cast of mixed).
     *
     * @param array<string, mixed> $options the HTTP client's options (query, json, body, headers...)
     */
    public function request(string $method, string $path, array $options = []): Data
    {
        $options['headers'] = (\is_array($options['headers'] ?? null) ? $options['headers'] : []) + ['Authorization' => 'Bearer '.$this->secretKey, 'Accept' => 'application/json'];
        Throttle::before($this->backoff);
        try {
            $response = $this->http->request($method, rtrim($this->baseUri, '/').'/'.ltrim($path, '/'), $options + ['timeout' => 15]);
            $status = $response->getStatusCode();
            $headers = $response->getHeaders(false);
            $body = $response->getContent(false);
        } catch (ExceptionInterface $e) {
            throw new ProviderException($this->provider, 'No answer: '.$e->getMessage(), null, $e);
        }
        Throttle::after($this->backoff, $this->provider, $status, $headers);

        $data = json_decode($body, true);
        if ($status >= 400) {
            $error = Data::of($data);

            throw new ProviderException($this->provider, \sprintf('HTTP %d: %s', $status, $error->string('message') ?? substr(trim($body), 0, 200)), $error->string('error') ?? (string) $status);
        }
        if (!\is_array($data)) {
            throw new ProviderException($this->provider, \sprintf('HTTP %d, not JSON: %s', $status, substr(trim($body), 0, 200)));
        }

        return Data::of($data);
    }

    /**
     * A payment as the provider answers it:
     * {"id", "status", "amount" (minor units), "currency", "checkout_url"}.
     */
    public function transaction(Data $payment): Transaction
    {
        $id = $payment->string('id');
        $status = self::STATUSES[$payment->text('status')] ?? null;
        if (null === $id || null === $status) {
            throw new ProviderException($this->provider, 'A payment without its id or a known status.');
        }
        $amount = $payment->int('amount');
        $currency = $payment->string('currency');
        $url = $payment->string('checkout_url');

        return new Transaction(
            provider: $this->provider,
            reference: $id,
            status: $status,
            amount: null !== $amount && null !== $currency ? Money::of($amount, strtoupper($currency)) : null,
            redirectUrl: Status::PENDING === $status ? $url : null,
            raw: $payment->toArray(),
        );
    }
}
