<?php

namespace __Namespace__;

use Omnisong\Catalog\CatalogFactory;
use Omnisong\Catalog\CatalogInterface;
use Omnisong\Config;

/**
 * "__name__": __Title__, from its options (none yet).
 *
 *   new __Name__CatalogFactory($httpClient)   // the application's Symfony\Contracts\HttpClient\HttpClientInterface
 *
 * The HTTP client is the application's, in $this->http: never created
 * inside; keep it with its retries off (omnisense/contracts'
 * Http\NoRetry::of()) and give the catalogue the Backoff CatalogFactory puts
 * in the Config ("omnisong.backoff"), so that the service's waits (a 429's
 * Retry-After) hold the next calls. Name the options it needs in
 * "omnisong.required_options". omnisong/itunes is a small real catalogue to
 * read.
 */
final class __Name__CatalogFactory extends CatalogFactory
{
    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnisong.factory_name' => '__name__',
            'omnisong.required_options' => [],
        ]);
    }

    protected function build(Config $c): CatalogInterface
    {
        return new __Name__Catalog();
    }
}
