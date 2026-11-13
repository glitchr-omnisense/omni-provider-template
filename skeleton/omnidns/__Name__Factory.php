<?php

namespace __Namespace__;

use Omnidns\Config;
use Omnidns\ProviderFactory;
use Omnidns\ProviderInterface;

/**
 * "__name__": __Title__, from its options (none yet).
 *
 * When the provider calls the service, take the application's HTTP client in
 * this constructor (Symfony\Contracts\HttpClient\HttpClientInterface, never
 * created here), keep it with its retries off (omnisense/contracts'
 * Http\NoRetry::of()) and give the provider $c->backoff() with it, so that
 * the service's waits (a 429's Retry-After) hold the next calls; name the
 * options it needs in "omnidns.required_options" ("api_key"...). omnidns/gandi is
 * a small real provider to read.
 */
final class __Name__Factory extends ProviderFactory
{
    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnidns.factory_name' => '__name__',
            'omnidns.factory_title' => '__Title__',
            'omnidns.required_options' => [],
        ]);
    }

    protected function build(Config $c): ProviderInterface
    {
        return new __Name__Provider();
    }
}
