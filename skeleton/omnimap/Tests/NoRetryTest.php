<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnimap\Exception\OmnimapException;
use Omnimap\Model\LatLng;
use Omnimap\Model\MapView;
use Omnimap\Model\Size;
use Omnimap\StaticMapInterface;
use Omnimap\Testing\ArrayCache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

/**
 * A tile the server refuses is not fetched again behind the provider's back:
 * given the application's client turned into a RetryableHttpClient
 * (framework.http_client's retry_failed, which resends a request answered
 * 503), the factory keeps it with its retries off (omnisense/contracts'
 * Http\NoRetry) - each tile is asked once, its 503 the provider's error.
 */
final class NoRetryTest extends TestCase
{
    /** @var list<string> "METHOD url" of each request that reached the wire */
    private array $sent = [];

    public function testTheRetryingClientIsNotBlind(): void
    {
        $this->retrying()->request('GET', 'https://tiles.example.org/1/0/0.png')->getStatusCode();
        self::assertCount(4, $this->sent, 'Symfony asks a tile answered 503 again, three times');
    }

    public function testEachTileIsAskedOnce(): void
    {
        $provider = (new __Name__Factory($this->retrying(), new ArrayCache()))->create(['tiles' => 'https://tiles.example.org/{z}/{x}/{y}.png']);
        self::assertInstanceOf(StaticMapInterface::class, $provider);
        try {
            $provider->image(new MapView(new LatLng(48.8566, 2.3522), 12), new Size(256, 256));
            self::fail('A 503 was a tile.');
        } catch (OmnimapException) {
        }
        self::assertNotSame([], $this->sent);
        self::assertSame(array_values(array_unique($this->sent)), $this->sent, 'no tile asked twice');
    }

    /** The application's client as a retrying one, every tile answered 503 - no delay, so a retry would show at once. */
    private function retrying(): RetryableHttpClient
    {
        $mock = new MockHttpClient(function (string $method, string $url): MockResponse {
            $this->sent[] = $method.' '.$url;

            return new MockResponse('Service Unavailable', ['http_code' => 503]);
        });

        return new RetryableHttpClient($mock, new GenericRetryStrategy(delayMs: 0), 3);
    }
}
