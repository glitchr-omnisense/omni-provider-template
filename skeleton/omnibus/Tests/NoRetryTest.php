<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnibus\Exception\CarrierException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

/**
 * Nothing is sent twice: given the application's client turned into a
 * RetryableHttpClient (framework.http_client's retry_failed, which resends a
 * POST answered 503), the factory keeps it with its retries off (omnibus'
 * GatewayFactory, omnisense/contracts' Http\NoRetry) - the 503 reaches the
 * gateway, a CarrierException. The template's only POST is the token
 * request; a label, a pickup or a cancellation goes through the same client.
 */
final class NoRetryTest extends TestCase
{
    /** @var list<string> "METHOD url" of each request that reached the wire */
    private array $sent = [];

    public function testTheRetryingClientIsNotBlind(): void
    {
        self::assertSame(200, $this->retrying()->request('POST', 'https://api.example.org/v1/oauth/token')->getStatusCode());
        self::assertCount(2, $this->sent, 'Symfony resends a POST answered 503');
    }

    public function testAPostAnswered503IsSentOnce(): void
    {
        $gateway = (new __Name__Factory($this->retrying()))->create(['client_id' => 'client', 'client_secret' => 'secret']);
        try {
            $gateway->track('1234567890');
            self::fail('A 503 was an answer.');
        } catch (CarrierException $e) {
            self::assertStringContainsString('503', $e->getMessage());
        }
        self::assertSame(['POST https://api.example.org/v1/oauth/token'], $this->sent);
    }

    /** The application's client as a retrying one: 503 first, then 200 - no delay, so a retry would show at once. */
    private function retrying(): RetryableHttpClient
    {
        $answers = [new MockResponse('{"error":"unavailable","message":"Service Unavailable"}', ['http_code' => 503]), new MockResponse('{"access_token":"token","expires_in":3600}')];
        $mock = new MockHttpClient(function (string $method, string $url) use (&$answers): MockResponse {
            $this->sent[] = $method.' '.$url;

            return array_shift($answers) ?? new MockResponse('', ['http_code' => 500]);
        });

        return new RetryableHttpClient($mock, new GenericRetryStrategy(delayMs: 0), 3);
    }
}
