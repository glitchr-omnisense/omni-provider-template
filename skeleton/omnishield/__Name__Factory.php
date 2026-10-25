<?php

namespace __Namespace__;

use Omnishield\Config;
use Omnishield\Exception\InvalidConfigException;
use Omnishield\GatewayFactory;
use Omnishield\GatewayInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__, from its options:
 *
 *   site_key  the widget's public key (required)
 *   secret    the site's secret, sent to the provider with each token (required)
 *   script    the widget's script
 *   field     the form field the token is posted in
 *   base_uri  the verification API's address
 *
 *   (new __Name__Factory($httpClient))->create(['site_key' => '...', 'secret' => '...']);
 *
 * The HTTP client is the application's, given to the constructor: the gateway
 * asks its provider about every token. Replace the defaults with the service's
 * and declare its other options here.
 */
final class __Name__Factory extends GatewayFactory
{
    public function __construct(private readonly ?HttpClientInterface $http = null)
    {
    }

    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnishield.factory_name' => '__name__',
            'omnishield.factory_title' => '__Title__',
            'omnishield.required_options' => ['site_key', 'secret'],
            'script' => __Name__Gateway::SCRIPT,
            'field' => __Name__Gateway::FIELD,
            'base_uri' => __Name__Gateway::BASE_URI,
        ]);
    }

    protected function build(Config $c): GatewayInterface
    {
        return new __Name__Gateway(
            $this->http ?? throw new InvalidConfigException('The __Title__ gateway asks its provider about each token: give its factory an HTTP client.'),
            (string) $c->string('site_key'),
            (string) $c->string('secret'),
            $c->string('script') ?? __Name__Gateway::SCRIPT,
            $c->string('field') ?? __Name__Gateway::FIELD,
            $c->string('base_uri') ?? __Name__Gateway::BASE_URI,
        );
    }
}
