<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use __Namespace__\__Name__Provider;
use Omniauth\OAuth\Authorization;
use Omniauth\OAuth\Provider\Provider;
use Omniauth\OAuth\Provider\ProviderFactory;
use Omniauth\Testing\ProviderContractTests;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * omniauth's provider contract on __Title__, offline: the server's answers are
 * queued in $answers and served in order. The template's are written in the
 * format its provider reads; replace them with the service's real answers
 * (recorded once, or written from its documentation - say which here).
 */
final class __Name__ContractTest extends TestCase
{
    use ProviderContractTests;

    private const CLIENT_ID = 'contract-client';
    private const CLIENT_SECRET = 'contract-secret-never-shown';

    /** @var list<MockResponse> */
    private array $answers = [];

    protected function provider(string $redirectUri): Provider
    {
        $http = new MockHttpClient(fn () => array_shift($this->answers) ?? throw new \LogicException('No answer queued.'));

        return new __Name__Provider($http, self::CLIENT_ID, self::CLIENT_SECRET, $redirectUri);
    }

    protected function consented(Provider $provider, Authorization $authorization, string $authorizationUrl): string
    {
        $this->answers[] = self::json(['access_token' => 'recorded-access-token', 'token_type' => 'Bearer', 'expires_in' => 3600]);
        $this->answers[] = self::json(['sub' => '248289761001', 'email' => 'camille@example.org', 'email_verified' => true, 'name' => 'Camille Durand']);

        return 'recorded-code';
    }

    protected function refused(Provider $provider): string
    {
        $this->answers[] = self::json(['error' => 'invalid_grant', 'error_description' => 'The code was already used.'], 400);

        return 'spent-code';
    }

    protected function unanswered(Provider $provider): string
    {
        $this->answers[] = new MockResponse('<html>Service Unavailable</html>', ['http_code' => 503, 'response_headers' => ['content-type' => 'text/html']]);

        return 'recorded-code';
    }

    protected function factory(): ProviderFactory
    {
        return new __Name__Factory(new MockHttpClient());
    }

    /** @return array<string, string> */
    protected function entry(): array
    {
        return ['type' => '__name__', 'client_id' => self::CLIENT_ID, 'client_secret' => self::CLIENT_SECRET, 'redirect_uri' => '/login/__name__/callback'];
    }

    protected function secret(): string
    {
        return self::CLIENT_SECRET;
    }

    /** An OAuth 2.0 provider without ID tokens: no nonce in its authorization address. */
    protected function sendsNonce(): bool
    {
        return false;
    }

    /** @param array<string, mixed> $json */
    private static function json(array $json, int $status = 200): MockResponse
    {
        return new MockResponse(json_encode($json, \JSON_THROW_ON_ERROR), ['http_code' => $status, 'response_headers' => ['content-type' => 'application/json']]);
    }
}
