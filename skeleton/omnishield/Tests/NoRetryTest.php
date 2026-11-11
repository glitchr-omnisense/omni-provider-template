<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnishield\ChallengeInterface;
use Omnishield\Exception\UnreachableException;
use Omnishield\Model\Attempt;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

/**
 * A token is sent once: given the application's client turned into a
 * RetryableHttpClient (framework.http_client's retry_failed, which resends a
 * POST answered 503), the factory keeps it with its retries off
 * (omnisense/contracts' Http\NoRetry) - the 503 reaches the gateway, which
 * says the provider did not answer; the token is not spent a second time.
 */
final class NoRetryTest extends TestCase
{
    /** @var list<string> "METHOD url" of each request that reached the wire */
    private array $sent = [];

    public function testTheRetryingClientIsNotBlind(): void
    {
        self::assertSame(200, $this->retrying()->request('POST', 'https://challenges.example.org/api/siteverify')->getStatusCode());
        self::assertCount(2, $this->sent, 'Symfony resends a POST answered 503');
    }

    public function testAVerificationAnswered503IsSentOnce(): void
    {
        $gateway = (new __Name__Factory($this->retrying()))->create(['site_key' => 'site-key', 'secret' => 'secret']);
        self::assertInstanceOf(ChallengeInterface::class, $gateway);
        try {
            $gateway->verify(new Attempt('token'));
            self::fail('A 503 was an answer.');
        } catch (UnreachableException $e) {
            self::assertStringContainsString('503', $e->getMessage());
        }
        self::assertSame(['POST https://challenges.example.org/api/siteverify'], $this->sent);
    }

    /** The application's client as a retrying one: 503 first, then 200 - no delay, so a retry would show at once. */
    private function retrying(): RetryableHttpClient
    {
        $answers = [new MockResponse('Service Unavailable', ['http_code' => 503]), new MockResponse('{"success": true, "hostname": "example.org"}')];
        $mock = new MockHttpClient(function (string $method, string $url) use (&$answers): MockResponse {
            $this->sent[] = $method.' '.$url;

            return array_shift($answers) ?? new MockResponse('', ['http_code' => 500]);
        });

        return new RetryableHttpClient($mock, new GenericRetryStrategy(delayMs: 0), 3);
    }
}
