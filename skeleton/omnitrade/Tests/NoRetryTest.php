<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnitrade\Exception\ProviderException;
use Omnitrade\Model\Money;
use Omnitrade\Model\Payment;
use Omnitrade\Registry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

/**
 * A payment is never sent twice: given the application's client turned
 * into a RetryableHttpClient (framework.http_client's retry_failed, which
 * resends a POST answered 503), the factory keeps it with its retries off
 * (omnitrade's GatewayFactory, omnisense/contracts' Http\NoRetry) - the 503
 * reaches the provider, which decides; nothing is replayed.
 */
final class NoRetryTest extends TestCase
{
    /** @var list<string> "METHOD url" of each request that reached the wire */
    private array $sent = [];

    public function testTheRetryingClientIsNotBlind(): void
    {
        self::assertSame(200, $this->retrying()->request('POST', 'https://api.example.org/v1/payments')->getStatusCode());
        self::assertCount(2, $this->sent, 'Symfony resends a POST answered 503');
    }

    public function testAPaymentAnswered503IsSentOnce(): void
    {
        $registry = new Registry([new __Name__Factory($this->retrying())], ['main' => ['factory' => '__name__', 'options' => ['secret_key' => 'sk_test_retry']]]);
        try {
            $registry->get('main')->purchase(new Payment(Money::of(1250, 'EUR'), 'order-1'));
            self::fail('A 503 was an answer.');
        } catch (ProviderException $e) {
            self::assertStringContainsString('503', $e->getMessage());
        }
        self::assertSame(['POST https://api.example.org/v1/payments'], $this->sent);
    }

    /** The application's client as a retrying one: 503 first, then 200 - no delay, so a retry would show at once. */
    private function retrying(): RetryableHttpClient
    {
        $answers = [new MockResponse('{"error":"unavailable","message":"Service Unavailable"}', ['http_code' => 503]), new MockResponse('{"id":"pay_1","status":"open","amount":1250,"currency":"eur","checkout_url":"https://pay.example.org/pay_1"}')];
        $mock = new MockHttpClient(function (string $method, string $url) use (&$answers): MockResponse {
            $this->sent[] = $method.' '.$url;

            return array_shift($answers) ?? new MockResponse('', ['http_code' => 500]);
        });

        return new RetryableHttpClient($mock, new GenericRetryStrategy(delayMs: 0), 3);
    }
}
