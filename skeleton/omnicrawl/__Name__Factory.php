<?php

namespace __Namespace__;

use Omnicrawl\Agent;
use Omnicrawl\Config;
use Omnicrawl\ProviderFactory;
use Omnicrawl\ProviderInterface;

/**
 * "__name__": __Title__, from its options: user_agent (required by every
 * omnicrawl factory, given by the Registry from its Gate: the application's
 * one identity, checked by Agent::parse() before the provider is built).
 *
 * When the provider calls the service, take the application's HTTP client in
 * this constructor (Symfony\Contracts\HttpClient\HttpClientInterface, never
 * created here), keep it with its retries off (omnisense/contracts'
 * Http\NoRetry::of()) and give the provider $c->backoff() with it, so that
 * the service's waits (a 429's Retry-After) hold the next calls; name the
 * options it needs in "omnicrawl.required_options" ("api_key" - the reference of the key, never the key). omnicrawl/scrapingbee is
 * a small real provider to read.
 */
final class __Name__Factory extends ProviderFactory
{
    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnicrawl.factory_name' => '__name__',
            'omnicrawl.factory_title' => '__Title__',
            'omnicrawl.required_options' => ['user_agent'],
        ]);
    }

    protected function build(Config $c, Agent $agent): ProviderInterface
    {
        return new __Name__Provider();
    }
}
