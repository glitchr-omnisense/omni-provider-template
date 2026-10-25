<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnishield\GatewayFactoryInterface;
use Omnishield\Model\Attempt;
use Omnishield\Testing\GatewayContractTests;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * omnishield's conformance suite on __Title__, offline: each recorded case is
 * a token, whether it must pass, and the provider's answers. The template's
 * are written in the format its gateway reads; replace them with the
 * service's real answers (recorded once with its testing keys, or written from
 * its documentation - say which here).
 */
final class __Name__ContractTest extends TestCase
{
    use GatewayContractTests;

    protected function factory(HttpClientInterface $http): GatewayFactoryInterface
    {
        return new __Name__Factory($http);
    }

    protected function options(): array
    {
        return ['site_key' => 'test-site-key', 'secret' => 'test-secret'];
    }

    protected function recordedCases(): iterable
    {
        $token = 'XXXX.RECORDED.TOKEN.XXXX';

        yield 'passes' => [new Attempt($token, '192.0.2.10', null, 'www.example.org'), true, [self::answer(['success' => true, 'hostname' => 'www.example.org', 'challenge_ts' => '2026-10-21T10:00:00Z', 'error-codes' => []])]];
        yield 'another host' => [new Attempt($token, '192.0.2.10', null, 'evil.example'), false, [self::answer(['success' => true, 'hostname' => 'www.example.org', 'challenge_ts' => '2026-10-21T10:00:00Z', 'error-codes' => []])]];
        yield 'invalid' => [new Attempt($token), false, [self::answer(['success' => false, 'error-codes' => ['invalid-input-response']])]];
        yield 'spent' => [new Attempt($token), false, [self::answer(['success' => false, 'error-codes' => ['timeout-or-duplicate']])]];
    }

    /** @param array<string, mixed> $json */
    private static function answer(array $json): MockResponse
    {
        return new MockResponse(json_encode($json, \JSON_THROW_ON_ERROR), ['response_headers' => ['content-type' => 'application/json']]);
    }
}
