<?php

namespace __Namespace__;

use Omnibus\Exception\CarrierException;
use Omnibus\Http\Throttle;
use Omnisense\Contracts\Policy\Backoff;
use Omnisense\Contracts\Policy\PolicyGuard;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__'s HTTP API: every call goes through send(), where its failures
 * become the family's CarrierException - an HTTP status of 400 or more, no
 * answer, a body that is not JSON (a proxy's page) - with the carrier's own
 * error code when it gives one; its answers are read through Data, never a
 * cast of mixed.
 *
 * Each call carries a bearer token, asked with OAuth's client credentials
 * (POST oauth/token) and kept until it expires. That request is one the
 * registry's guard does not see: it goes through the guard the factory gives
 * (Config::guard()), under the operation "token", so that a limit the carrier
 * sets on token requests is counted, and refused before it is sent.
 *
 * Given the gateway's Backoff (the factory's Config::backoff()), a 429 is a
 * RateLimitedException and the carrier's Retry-After holds every next call,
 * refused without a request (omnibus' Http\Throttle). Replace the address, the
 * authentication and the error's fields with the service's.
 */
final class Api
{
    private ?string $token = null;

    private int $expires = 0;

    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $carrier,
        private readonly string $clientId,
        #[\SensitiveParameter] private readonly string $clientSecret,
        private readonly string $baseUri,
        /** The waits the carrier asks for, shared by the gateway's calls (omnisense/contracts' Backoff). */
        private readonly ?Backoff $backoff = null,
        /** The guard the token requests are counted by, under "token"; null: not counted (an Api built by hand). */
        private readonly ?PolicyGuard $guard = null,
    ) {
    }

    /**
     * A call with the bearer token.
     *
     * @param array<string, mixed> $options the HTTP client's options (query, json, body...)
     */
    public function request(string $method, string $path, array $options = []): Data
    {
        return $this->send($method, $path, ['headers' => ['Authorization' => 'Bearer '.$this->token(), 'Accept' => 'application/json']] + $options);
    }

    /** The bearer token: the one kept while it lasts, else a new one, its request counted under "token". */
    private function token(): string
    {
        if (null !== $this->token && time() < $this->expires) {
            return $this->token;
        }
        $ask = fn (): Data => $this->send('POST', 'oauth/token', ['body' => ['grant_type' => 'client_credentials', 'client_id' => $this->clientId, 'client_secret' => $this->clientSecret]]);
        $answer = null === $this->guard ? $ask() : $this->guard->call('token', $ask);
        $this->token = $answer->string('access_token') ?? throw new CarrierException($this->carrier, 'The token answer carries no access_token.');
        // Renewed a minute before it expires; one without expires_in serves this call only.
        $this->expires = time() + max(0, ($answer->int('expires_in') ?? 0) - 60);

        return $this->token;
    }

    /** @param array<string, mixed> $options */
    private function send(string $method, string $path, array $options): Data
    {
        Throttle::before($this->backoff);
        try {
            $response = $this->http->request($method, rtrim($this->baseUri, '/').'/'.ltrim($path, '/'), $options + ['timeout' => 15]);
            $status = $response->getStatusCode();
            $headers = $response->getHeaders(false);
            $body = $response->getContent(false);
        } catch (ExceptionInterface $e) {
            throw new CarrierException($this->carrier, 'No answer: '.$e->getMessage(), null, $e);
        }
        Throttle::after($this->backoff, $this->carrier, $status, $headers);

        $data = json_decode($body, true);
        if ($status >= 400) {
            $error = Data::of($data);

            throw new CarrierException($this->carrier, \sprintf('HTTP %d: %s', $status, $error->string('message') ?? substr(trim($body), 0, 200)), $error->string('error') ?? (string) $status);
        }
        if (!\is_array($data)) {
            throw new CarrierException($this->carrier, \sprintf('HTTP %d, not JSON: %s', $status, substr(trim($body), 0, 200)));
        }

        return Data::of($data);
    }
}
