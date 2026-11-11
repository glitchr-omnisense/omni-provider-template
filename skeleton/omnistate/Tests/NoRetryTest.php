<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__;
use Omnistate\Exception\UnavailableException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

/**
 * Nothing is sent again behind the register's back: given the application's
 * client turned into a RetryableHttpClient (framework.http_client's
 * retry_failed, which resends a request answered 503 or 429), the register
 * keeps it with its retries off (omnisense/contracts' Http\NoRetry) - the
 * 503 reaches it, and Omnistate's guard decides what comes next.
 */
final class NoRetryTest extends TestCase
{
    /** @var list<string> "METHOD url" of each request that reached the wire */
    private array $sent = [];

    public function testTheRetryingClientIsNotBlind(): void
    {
        self::assertSame(200, $this->retrying()->request('GET', 'https://registry.example.org/api/companies/901821074')->getStatusCode());
        self::assertCount(2, $this->sent, 'Symfony resends a request answered 503');
    }

    public function testALookupAnswered503IsSentOnce(): void
    {
        try {
            (new __Name__($this->retrying()))->company('901821074');
            self::fail('A 503 was an answer.');
        } catch (UnavailableException $e) {
            self::assertStringContainsString('503', $e->getMessage());
        }
        self::assertSame(['GET https://registry.example.org/api/companies/901821074'], $this->sent);
    }

    /** The application's client as a retrying one: 503 first, then 200 - no delay, so a retry would show at once. */
    private function retrying(): RetryableHttpClient
    {
        $answers = [new MockResponse('Service Unavailable', ['http_code' => 503]), new MockResponse('{"id": "901821074", "name": "GLITCH ART", "status": "active", "country": "FR"}')];
        $mock = new MockHttpClient(function (string $method, string $url) use (&$answers): MockResponse {
            $this->sent[] = $method.' '.$url;

            return array_shift($answers) ?? new MockResponse('', ['http_code' => 500]);
        });

        return new RetryableHttpClient($mock, new GenericRetryStrategy(delayMs: 0), 3);
    }
}
