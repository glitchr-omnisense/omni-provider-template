<?php

namespace __Namespace__;

use Omnischolar\Config;
use Omnischolar\Exception\InvalidConfigException;
use Omnischolar\Source\SourceFactory;
use Omnischolar\Source\SourceInterface;

/**
 * "__name__": __Title__, from its options - those every omnischolar source takes
 * (base_uri, mailto, user_agent, throttle, policy, usage, max_wait), and
 * the ones the service needs, named in "omnischolar.required_options"
 * ("api_key"...).
 *
 *   new __Name__SourceFactory($httpClient)   // the application's Symfony\Contracts\HttpClient\HttpClientInterface
 *
 * The HTTP client is the application's, given here: never created inside.
 * SourceFactory::create() puts the source under its usage policy (policy.json,
 * as the site tightens it). omnischolar/orcid is a small real source to read.
 */
final class __Name__SourceFactory extends SourceFactory
{
    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnischolar.factory_name' => '__name__',
            'omnischolar.required_options' => [],
            'base_uri' => __Name__Source::BASE_URI,
        ]);
    }

    protected function build(Config $c): SourceInterface
    {
        return new __Name__Source(
            $this->http ?? throw new InvalidConfigException('The "__name__" source needs the application\'s HTTP client, given to __Name__SourceFactory.'),
            $c->string('base_uri') ?? __Name__Source::BASE_URI,
            ['User-Agent' => self::userAgent($c)],
            $c->float('throttle'),
        );
    }
}
