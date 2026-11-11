<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Provider;
use Omniauth\OAuth\Authorization;
use Omniauth\OAuth\Exception\ProviderError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

/**
 * A code is exchanged once: given the application's client turned into a
 * RetryableHttpClient (framework.http_client's retry_failed, which resends a
 * POST answered 503), the provider keeps it with its retries off
 * (omnisense/contracts' Http\NoRetry) - the 503 reaches it, and the sign-in
 * is refused rather than the code sent again.
 */
final class NoRetryTest extends TestCase
{
    /** @var list<string> "METHOD url" of each request that reached the wire */
    private array $sent = [];

    public function testTheRetryingClientIsNotBlind(): void
    {
        self::assertSame(200, $this->retrying()->request('POST', __Name__Provider::TOKEN_URL)->getStatusCode());
        self::assertCount(2, $this->sent, 'Symfony resends a POST answered 503');
    }

    public function testAnExchangeAnswered503IsSentOnce(): void
    {
        $provider = new __Name__Provider($this->retrying(), 'client', 'secret', 'https://www.example.org/login/__name__/callback');
        try {
            $provider->identify('code', Authorization::fresh());
            self::fail('A 503 was an answer.');
        } catch (ProviderError) {
        }
        self::assertSame(['POST '.__Name__Provider::TOKEN_URL], $this->sent);
    }

    /** The application's client as a retrying one: 503 first, then 200 - no delay, so a retry would show at once. */
    private function retrying(): RetryableHttpClient
    {
        $answers = [new MockResponse('Service Unavailable', ['http_code' => 503]), new MockResponse('{"access_token": "token", "token_type": "Bearer"}')];
        $mock = new MockHttpClient(function (string $method, string $url) use (&$answers): MockResponse {
            $this->sent[] = $method.' '.$url;

            return array_shift($answers) ?? new MockResponse('', ['http_code' => 500]);
        });

        return new RetryableHttpClient($mock, new GenericRetryStrategy(delayMs: 0), 3);
    }
}
