<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use __Namespace__\__Name__Gateway;
use Omnisense\Contracts\Policy\PolicyExport;
use Omnisense\Contracts\Policy\UsagePolicy;
use Omnishield\Exception\UnreachableException;
use Omnishield\Exception\UsageLimitReachedException;
use Omnishield\Model\Attempt;
use Omnishield\Registry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * __Title__'s usage policy: declared as data (policy.json, named in
 * composer.json's extra.omnisense.policy), given by the gateway's
 * capabilities, and honoured: a site's limit refuses a call before it is
 * made, a 429's Retry-After holds the next calls. Once the terms are read,
 * assert their figures here.
 */
final class PolicyTest extends TestCase
{
    private const OPTIONS = ['site_key' => 'site-key', 'secret' => 'secret'];

    public function testThePackageDeclaresItsPolicy(): void
    {
        $declared = (new PolicyExport())->folders(\dirname(__DIR__))->entries();
        self::assertCount(1, $declared, 'composer.json\'s extra.omnisense.policy names it');
        self::assertSame(['__package__', 'policy.json'], [$declared[0]['package'], $declared[0]['file']]);
        self::assertArrayNotHasKey('error', $declared[0], 'it reads as the schema says');

        $policy = UsagePolicy::fromFile(__Name__Gateway::POLICY);
        self::assertSame('__name__', $policy->provider, 'named after the factory');
        self::assertNotNull($policy->provenance->terms, 'it links to the terms it was read from');
        $gateway = (new __Name__Factory(new MockHttpClient()))->create(self::OPTIONS);
        self::assertEquals($policy, $gateway->capabilities()->policy());
    }

    public function testASitesLimitRefusesTheCallBeforeItIsMade(): void
    {
        $requests = 0;
        $http = new MockHttpClient(static function () use (&$requests) {
            ++$requests;

            return new MockResponse('{"success": true, "hostname": "example.org"}');
        });
        $registry = new Registry([new __Name__Factory($http)], ['site' => ['factory' => '__name__', 'options' => self::OPTIONS, 'policy' => ['rateLimits' => [['max' => 1, 'window' => 'minute']]]]]);
        $registry->challenge('site')->verify(new Attempt('token'));
        try {
            $registry->challenge('site')->verify(new Attempt('token'));
            self::fail('A call over the site\'s limit was made.');
        } catch (UsageLimitReachedException $e) {
            self::assertGreaterThan(0, $e->retryAfter);
        }
        self::assertSame(1, $requests, 'refused before any request');
    }

    public function testA429AndItsRetryAfterHoldTheNextCall(): void
    {
        $requests = 0;
        $http = new MockHttpClient(static function () use (&$requests) {
            ++$requests;

            return new MockResponse('Too Many Requests', ['http_code' => 429, 'response_headers' => ['retry-after' => '60']]);
        });
        $registry = new Registry([new __Name__Factory($http)], ['site' => ['factory' => '__name__', 'options' => self::OPTIONS]]);
        foreach ([60, null] as $wait) {
            try {
                $registry->challenge('site')->verify(new Attempt('token'));
                self::fail('A 429 was an answer.');
            } catch (UnreachableException $e) {
                null === $wait ? self::assertGreaterThan(0, $e->retryAfter()) : self::assertSame($wait, $e->retryAfter());
            }
        }
        self::assertSame(1, $requests, 'the second call was held, nothing sent');
    }
}
