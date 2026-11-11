<?php

namespace __Namespace__;

use Omniauth\OAuth\Authorization;
use Omniauth\OAuth\Exception\ProviderError;
use Omniauth\OAuth\Identity;
use Omniauth\OAuth\Provider\Provider;
use Omnisense\Contracts\Http\NoRetry;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__, an OAuth 2.0 identity provider with a user-info endpoint: the
 * visitor sent to AUTHORIZE_URL with the trip's state and PKCE challenge, the
 * code that comes back exchanged at TOKEN_URL (with the verifier and the same
 * return address), the access token then shown to USERINFO_URL, whose sub,
 * email, email_verified and name make the Identity.
 *
 * Replace the addresses, SCOPES and the user-info fields with the service's.
 * An OpenID Connect service also sends the nonce and checks it in the ID token
 * the exchange answers (sendsNonce() true in the contract test).
 */
final class __Name__Provider implements Provider
{
    public const AUTHORIZE_URL = 'https://accounts.example.org/oauth/authorize';
    public const TOKEN_URL = 'https://accounts.example.org/oauth/token';
    public const USERINFO_URL = 'https://accounts.example.org/oauth/userinfo';
    public const SCOPES = ['profile', 'email'];

    private readonly HttpClientInterface $http;

    /**
     * The HTTP client is the application's, kept with its retries off
     * (omnisense/contracts' Http\NoRetry): a code is exchanged once, and a
     * retrying client (framework.http_client's retry_failed) would send it
     * again after a 503, the second answer an invalid_grant
     * (Tests/NoRetryTest.php).
     *
     * @param list<string> $scopes
     */
    public function __construct(
        HttpClientInterface $http,
        private readonly string $clientId,
        #[\SensitiveParameter] private readonly string $clientSecret,
        private readonly string $redirectUri,
        private readonly array $scopes = self::SCOPES,
        private readonly string $name = '__name__',
        private readonly string $authorizeUrl = self::AUTHORIZE_URL,
        private readonly string $tokenUrl = self::TOKEN_URL,
        private readonly string $userinfoUrl = self::USERINFO_URL,
    ) {
        $this->http = NoRetry::of($http);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function authorizationUrl(Authorization $authorization): string
    {
        return $this->authorizeUrl.'?'.http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $authorization->returnAddress($this->redirectUri),
            'scope' => implode(' ', $this->scopes),
            'state' => $authorization->state,
            'code_challenge' => $authorization->challenge(),
            'code_challenge_method' => 'S256',
        ], '', '&', \PHP_QUERY_RFC3986);
    }

    public function identify(string $code, Authorization $authorization): Identity
    {
        $token = $this->call('POST', $this->tokenUrl, ['body' => [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $authorization->returnAddress($this->redirectUri),
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code_verifier' => $authorization->verifier,
        ]]);
        $accessToken = $token->string('access_token');
        if (null === $accessToken) {
            throw new ProviderError(ProviderError::EXCHANGE, 'The token answer carries no access_token.');
        }

        $profile = $this->call('GET', $this->userinfoUrl, ['headers' => ['Authorization' => 'Bearer '.$accessToken]]);
        $subject = $profile->string('sub');
        if (null === $subject) {
            throw new ProviderError(ProviderError::SUBJECT, 'The profile carries no subject.');
        }
        $email = $profile->string('email');
        $email = null !== $email && str_contains($email, '@') ? $email : null;

        return new Identity(
            provider: $this->name,
            subject: $subject,
            email: $email,
            emailVerified: null !== $email && Identity::verified($profile->get('email_verified') ?? false),
            name: $profile->string('name'),
            claims: Data::map($profile->toArray()),
        );
    }

    /**
     * A JSON answer of the provider, read through Data (never a cast of
     * mixed); a refusal (HTTP 400 or more), no answer or an unreadable one is
     * a ProviderError - its message never showing the secret, which travels
     * in the request's body only.
     *
     * @param array<string, mixed> $options
     */
    private function call(string $method, string $url, array $options): Data
    {
        try {
            $response = $this->http->request($method, $url, $options + ['headers' => ['Accept' => 'application/json'], 'timeout' => 15]);
            $status = $response->getStatusCode();
            $body = $response->getContent(false);
        } catch (ExceptionInterface $e) {
            throw new ProviderError(ProviderError::EXCHANGE, \sprintf('__Title__ did not answer %s.', parse_url($url, \PHP_URL_PATH)), $e);
        }
        $data = json_decode($body, true);
        if ($status >= 400) {
            $error = Data::of($data)->string('error') ?? 'HTTP '.$status;

            throw new ProviderError(ProviderError::EXCHANGE, \sprintf('__Title__ refused %s: %s.', parse_url($url, \PHP_URL_PATH), $error));
        }
        if (!\is_array($data)) {
            throw new ProviderError(ProviderError::EXCHANGE, \sprintf('__Title__ answered %s with what is not JSON.', parse_url($url, \PHP_URL_PATH)));
        }

        return Data::of($data);
    }
}
