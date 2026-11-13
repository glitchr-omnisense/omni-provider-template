<?php

namespace __Namespace__;

use Omnishow\Config;
use Omnishow\ProviderFactory;
use Omnishow\ProviderInterface;

/**
 * "__name__": __Title__, from its options (none yet).
 *
 * When the provider calls the service, take the application's HTTP client in
 * this constructor (Symfony\Contracts\HttpClient\HttpClientInterface, never
 * created here), keep it with its retries off (omnisense/contracts'
 * Http\NoRetry::of()) and give the provider omnishow's Http\JsonClient over
 * it with $c->backoff(), so that the service's waits (a 429's Retry-After)
 * hold the next calls; name the options it needs in
 * "omnishow.required_options" ("api_key"...). omnishow/omdb is the smallest
 * real provider to read.
 */
final class __Name__Factory extends ProviderFactory
{
    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnishow.factory_name' => '__name__',
            'omnishow.factory_title' => '__Title__',
            'omnishow.required_options' => [],
        ]);
    }

    protected function build(Config $c): ProviderInterface
    {
        return new __Name__Provider();
    }
}
