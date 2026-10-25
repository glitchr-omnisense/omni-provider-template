<?php

namespace __Namespace__;

use Omniauth\OAuth\Provider\Provider;
use Omniauth\OAuth\Provider\ProviderFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__ from a host's configuration, for omniauth's
 * Providers::configured(): an entry named "__name__", or of "type: __name__".
 *
 *     __name__: { client_id: …, client_secret: …, redirect_uri: /login/__name__/callback, scopes: [...] }
 *
 * redirect_uri is an absolute address or a path (each trip's origin completes
 * it); scopes default to __Name__Provider::SCOPES; authorize_url, token_url
 * and userinfo_url default to the service's. The HTTP client is the
 * application's, given to the constructor. Replace the defaults with the
 * service's and read its own keys here.
 */
final class __Name__Factory implements ProviderFactory
{
    public function __construct(private readonly HttpClientInterface $http)
    {
    }

    public function create(string $name, array $entry): ?Provider
    {
        if ('__name__' !== ($entry['type'] ?? ('__name__' === $name ? '__name__' : null))) {
            return null;
        }
        $scopes = array_values(array_filter(array_map(static fn (mixed $scope): string => \is_scalar($scope) ? (string) $scope : '', (array) ($entry['scopes'] ?? [])), static fn (string $scope) => '' !== $scope));

        return new __Name__Provider(
            $this->http,
            self::string($entry, 'client_id'),
            self::string($entry, 'client_secret'),
            self::string($entry, 'redirect_uri'),
            $scopes ?: __Name__Provider::SCOPES,
            $name,
            self::string($entry, 'authorize_url') ?: __Name__Provider::AUTHORIZE_URL,
            self::string($entry, 'token_url') ?: __Name__Provider::TOKEN_URL,
            self::string($entry, 'userinfo_url') ?: __Name__Provider::USERINFO_URL,
        );
    }

    /** @param array<string, mixed> $entry */
    private static function string(array $entry, string $key): string
    {
        $value = $entry[$key] ?? '';

        return \is_scalar($value) ? (string) $value : '';
    }
}
