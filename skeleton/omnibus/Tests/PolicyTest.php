<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnibus\Exception\UsageLimitReachedException;
use Omnibus\Model\Address;
use Omnibus\Registry;
use Omnisense\Contracts\Policy\PolicyExport;
use Omnisense\Contracts\Policy\UsagePolicy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * __Title__'s usage policy: declared as data (policy.json, named in
 * composer.json's extra.omnisense.policy), the gateway's, and counted where
 * the registry's guard does not see: the token request under "token", each
 * further page of pickup points under "pickupPoints", each parcel's tracking
 * under "trackParcel", per parcel. The conformance suite
 * checks the rest (a 429 held, a site's limit on a family operation). Once
 * the terms are read, assert their figures here.
 */
final class PolicyTest extends TestCase
{
    /** @var list<string> "METHOD url" of each request sent */
    private array $sent = [];

    public function testThePackageDeclaresItsPolicy(): void
    {
        $declared = (new PolicyExport())->folders(\dirname(__DIR__))->entries();
        self::assertCount(1, $declared, 'composer.json\'s extra.omnisense.policy names it');
        self::assertSame(['__package__', 'policy.json'], [$declared[0]['package'], $declared[0]['file']]);
        self::assertArrayNotHasKey('error', $declared[0], 'it reads as the schema says');

        $policy = UsagePolicy::fromFile(__Name__Factory::POLICY);
        self::assertSame(str_replace('-', '_', '__name__'), $policy->provider, 'named after the factory');
        self::assertNotNull($policy->provenance->terms, 'it links to the terms it was read from');
        self::assertEquals($policy, $this->registry(3600)->policy('main'));
    }

    public function testATokenRequestIsCountedAndRefusedUnsentOverTheLimit(): void
    {
        // A token that lasts no time: each call asks a new one.
        $registry = $this->registry(0, ['rateLimits' => [['max' => 1, 'window' => 'day', 'operations' => ['token']]]]);
        $registry->get('main')->track('1234567890');
        try {
            $registry->get('main')->track('1234567890');
            self::fail('A second token request in the day the site allows one.');
        } catch (UsageLimitReachedException $e) {
            self::assertGreaterThan(0, $e->retryAfter);
        }
        self::assertSame(['POST https://api.example.org/v1/oauth/token', 'GET https://api.example.org/v1/tracking/1234567890'], $this->sent, 'the second token request was never sent');
    }

    public function testAParcelsTrackingIsCountedPerParcel(): void
    {
        $registry = $this->registry(3600, ['rateLimits' => [['max' => 1, 'window' => 'day', 'scope' => 'resource', 'per' => 'parcel', 'operations' => ['trackParcel']]]]);
        $registry->get('main')->track('1234567890');
        try {
            $registry->get('main')->track('1234567890');
            self::fail('The same parcel tracked twice in the day the site allows once.');
        } catch (UsageLimitReachedException $e) {
            self::assertGreaterThan(0, $e->retryAfter);
        }
        $registry->get('main')->track('0987654321');
        self::assertSame([
            'POST https://api.example.org/v1/oauth/token',
            'GET https://api.example.org/v1/tracking/1234567890',
            'GET https://api.example.org/v1/tracking/0987654321',
        ], $this->sent, 'the second tracking of the same parcel was never sent; another parcel was');
    }

    public function testEachFurtherPageOfPickupPointsIsCounted(): void
    {
        // Three a day: the registry's guarded call (its first page), then its second and third pages.
        $registry = $this->registry(3600, ['rateLimits' => [['max' => 3, 'window' => 'day', 'operations' => ['pickupPoints']]]]);
        $near = new Address('Example', ['1 rue Oberkampf'], '75011', 'Paris', 'FR');
        self::assertCount(3, $registry->guarded('main')->pickupPoints($near, 3), 'one point a page');
        try {
            $registry->guarded('main')->pickupPoints($near, 1);
            self::fail('A fourth pickupPoints request in the day the site allows three.');
        } catch (UsageLimitReachedException $e) {
            self::assertGreaterThan(0, $e->retryAfter);
        }
        self::assertSame([
            'POST https://api.example.org/v1/oauth/token',
            'GET https://api.example.org/v1/pickup-points?page=1&postcode=75011&country=FR',
            'GET https://api.example.org/v1/pickup-points?page=2&postcode=75011&country=FR',
            'GET https://api.example.org/v1/pickup-points?page=3&postcode=75011&country=FR',
        ], $this->sent, 'the three pages counted, the next listing refused unsent');
    }

    /** @param array<string, mixed> $policy the site's tightening */
    private function registry(int $tokenLifetime, array $policy = []): Registry
    {
        $http = new MockHttpClient(function (string $method, string $url) use ($tokenLifetime): MockResponse {
            $this->sent[] = $method.' '.$url;

            return new MockResponse((string) json_encode(match (true) {
                str_ends_with($url, '/oauth/token') => ['access_token' => 'token', 'expires_in' => $tokenLifetime],
                1 === preg_match('~/pickup-points\?page=(\d+)~', $url, $m) => ['points' => [['id' => 'PP-'.$m[1], 'name' => 'Relay', 'postcode' => '75011', 'city' => 'Paris', 'country' => 'FR']], 'next_page' => (int) $m[1] + 1],
                default => ['tracking_number' => basename($url), 'status' => 'in_transit', 'events' => []],
            }));
        });

        return new Registry([new __Name__Factory($http)], ['main' => array_filter([
            'factory' => str_replace('-', '_', '__name__'),
            'options' => ['client_id' => 'client', 'client_secret' => 'secret'],
            'policy' => $policy,
        ])]);
    }
}
