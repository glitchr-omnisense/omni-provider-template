<?php

namespace __Namespace__;

use Omnipost\Config;
use Omnipost\ProviderFactory;
use Omnipost\ProviderInterface;

/**
 * "__name__": __Title__, from its options (none yet).
 *
 *   new __Name__ProviderFactory($httpClient)   // the application's Symfony\Contracts\HttpClient\HttpClientInterface
 *
 * ProviderFactory keeps the application's HTTP client with its retries off
 * (Http\NoRetry: a post is never published twice) in $this->http, and puts
 * the network's Backoff in the Config for its Api to observe every answer
 * with. Name the options the network needs in "omnipost.required_options"
 * (an account, a token...). omnipost/instagram is a real provider to read.
 */
final class __Name__ProviderFactory extends ProviderFactory
{
    protected function populate(Config $config): void
    {
        $config->defaults([
            'omnipost.factory_name' => '__name__',
            'omnipost.required_options' => [],
        ]);
    }

    protected function build(Config $config): ProviderInterface
    {
        return new __Name__Provider();
    }
}
