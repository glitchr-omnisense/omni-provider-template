<?php

namespace __Namespace__;

use Omnimap\Config;
use Omnisense\Contracts\Http\NoRetry;
use Omnimap\ProviderFactory;
use Omnimap\ProviderInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__, from its options:
 *
 *   tiles        the tile URL, {z}/{x}/{y} in it (required)
 *   attribution  as HTML, shown on the map
 *   max_zoom     19
 *   user_agent   sent with every tile the server fetches (images made of tiles)
 *
 * The HTTP client and the cache are the application's, given here: never created inside. The
 * client is kept with its retries off (omnisense/contracts' Http\NoRetry): a retrying client
 * (framework.http_client's retry_failed) would fetch a tile a server refused with 429 or 503 again
 * and again, requests nobody counted against the tile server's policy (Tests/NoRetryTest.php).
 */
final class __Name__Factory extends ProviderFactory
{
    private readonly ?HttpClientInterface $http;

    public function __construct(
        ?HttpClientInterface $http = null,
        private readonly ?CacheInterface $cache = null,
    ) {
        $this->http = null !== $http ? NoRetry::of($http) : null;
    }

    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnimap.factory_name' => '__name__',
            'omnimap.factory_title' => '__Title__',
            'omnimap.required_options' => ['tiles'],
            'attribution' => '',
            'max_zoom' => 19,
            'user_agent' => '',
        ]);
    }

    protected function build(Config $c): ProviderInterface
    {
        return new __Name__Provider(
            tiles: (string) $c->string('tiles'),
            attribution: (string) $c->string('attribution'),
            maxZoom: $c->int('max_zoom', 19),
            userAgent: $c->string('user_agent'),
            http: $this->http,
            cache: $this->cache,
        );
    }
}
