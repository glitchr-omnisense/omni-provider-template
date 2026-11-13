<?php

namespace __Namespace__;

use Omnifood\Config;
use Omnifood\PlatformFactory;
use Omnifood\PlatformInterface;

/**
 * "__name__": __Title__, from its options (none yet).
 *
 *   new __Name__PlatformFactory($httpClient)   // the application's Symfony\Contracts\HttpClient\HttpClientInterface
 *
 * PlatformFactory keeps the application's HTTP client with its retries off
 * (Http\NoRetry: an order's acceptance is never sent twice) in $this->http,
 * and puts the platform's Backoff in the Config ($config->backoff()) for its
 * Api to observe every answer with. A credential is best asked for when a
 * call needs it rather than required here, so that the platform still
 * answers what needs none (capabilities(), a webhook's signature).
 * omnifood/zenchef is a small real platform to read.
 */
final class __Name__PlatformFactory extends PlatformFactory
{
    protected function populate(Config $config): void
    {
        $config->defaults([
            'omnifood.factory_name' => '__name__',
            'omnifood.required_options' => [],
        ]);
    }

    protected function build(Config $config): PlatformInterface
    {
        return new __Name__Platform();
    }
}
