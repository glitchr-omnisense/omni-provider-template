<?php

namespace __Namespace__;

use Omnitravel\Config;
use Omnitravel\ProviderFactory;
use Omnitravel\ProviderInterface;

/**
 * "__name__": __Title__, from its options (none yet).
 *
 * When the provider calls the service, take the application's HTTP client in
 * this constructor (Symfony\Contracts\HttpClient\HttpClientInterface, never
 * created here), keep it with its retries off (omnisense/contracts'
 * Http\NoRetry::of()) and give the provider $c->backoff() with it, so that
 * the service's waits (a 429's Retry-After) hold the next calls; name the
 * options it needs in "omnitravel.required_options" ("api_key"...). omnitravel/transitous is
 * a small real provider to read.
 */
final class __Name__Factory extends ProviderFactory
{
    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnitravel.factory_name' => '__name__',
            'omnitravel.factory_title' => '__Title__',
            'omnitravel.required_options' => [],
        ]);
    }

    protected function build(Config $c): ProviderInterface
    {
        return new __Name__Provider();
    }
}
