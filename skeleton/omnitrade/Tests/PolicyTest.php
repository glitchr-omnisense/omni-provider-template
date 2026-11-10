<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnisense\Contracts\Policy\PolicyExport;
use Omnisense\Contracts\Policy\UsagePolicy;
use Omnitrade\Exception\RateLimitedException;
use Omnitrade\Exception\UsageLimitReachedException;
use Omnitrade\Registry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * __Title__'s usage policy: declared as data (policy.json, named in
 * composer.json's extra.omnisense.policy), the registry's policy for the
 * gateway, and honoured: a site's limit refuses a call before it is sent, a
 * 429's Retry-After holds the next calls. Once the terms are read, assert
 * their figures here (the limits per operation, the rights, the date).
 */
final class PolicyTest extends TestCase
{
    private int $requests = 0;

    public function testThePackageDeclaresItsPolicy(): void
    {
        $declared = (new PolicyExport())->folders(\dirname(__DIR__))->entries();
        self::assertCount(1, $declared, 'composer.json\'s extra.omnisense.policy names it');
        self::assertSame(['__package__', 'policy.json'], [$declared[0]['package'], $declared[0]['file']]);
        self::assertArrayNotHasKey('error', $declared[0], 'it reads as the schema says');

        $policy = UsagePolicy::fromFile(\dirname(__DIR__).'/policy.json');
        self::assertSame('__name__', $policy->provider, 'named after the factory');
        self::assertNotNull($policy->provenance->terms, 'it links to the terms it was read from');
        self::assertEquals($policy, $this->registry(new MockResponse('{}'))->policy('main'));
    }

    public function testASitesLimitRefusesTheCallBeforeItIsSent(): void
    {
        $paid = (string) json_encode(['id' => 'pay_1', 'status' => 'paid', 'amount' => 1250, 'currency' => 'eur']);
        $registry = $this->registry(new MockResponse($paid), ['rateLimits' => [['max' => 1, 'window' => 'minute']]]);
        $registry->guarded('main')->fetch('pay_1');
        try {
            $registry->guarded('main')->fetch('pay_1');
            self::fail('A call over the site\'s limit was sent.');
        } catch (UsageLimitReachedException $e) {
            self::assertGreaterThan(0, $e->retryAfter);
        }
        self::assertSame(1, $this->requests, 'refused before any request');
    }

    public function testA429AndItsRetryAfterHoldTheNextCalls(): void
    {
        $registry = $this->registry(new MockResponse('{"error":"rate_limited","message":"Too many requests"}', ['http_code' => 429, 'response_headers' => ['retry-after' => '30']]));
        try {
            $registry->get('main')->fetch('pay_1');
            self::fail('A 429 was an answer.');
        } catch (RateLimitedException $e) {
            self::assertSame(30, $e->retryAfter);
        }
        try {
            $registry->get('main')->fetch('pay_1');
            self::fail('The call after a 429 was sent.');
        } catch (UsageLimitReachedException) {
        }
        self::assertSame(1, $this->requests, 'the held call was not sent');
    }

    /** @param array<string, mixed> $policy the site's tightening */
    private function registry(MockResponse $answer, array $policy = []): Registry
    {
        $http = new MockHttpClient(function () use ($answer): MockResponse {
            ++$this->requests;

            return $answer;
        });

        return new Registry([new __Name__Factory($http)], ['main' => array_filter(['factory' => '__name__', 'options' => ['secret_key' => 'sk_test_policy'], 'policy' => $policy])]);
    }
}
