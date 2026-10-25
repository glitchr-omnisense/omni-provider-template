<?php

namespace __Namespace__;

use __Namespace__\Action\TrackingAction;
use Omnibus\Config;
use Omnibus\GatewayFactory;

/**
 * __Title__, from its options:
 *
 *   api_key   the account's key, sent as a bearer token (required)
 *   base_uri  the API's address
 *   rates     the shop's prices, in place of a rating API (Omnibus\Action\ConfiguredRatingAction)
 *
 *   (new __Name__Factory($httpClient))->create(['api_key' => '...', 'rates' => [...]]);
 *
 * The template's carrier tracks a parcel over its API (Action\TrackingAction,
 * through Api) and prices from the "rates" option; every other request is
 * refused as not supported. To make it the service's, put its address and its
 * authentication in Api, add one action per request it answers (Rating,
 * Shipping, Pickup, GetSlip, Cancel, Paperless: omnibus' docs/providers.md)
 * and declare each in populateConfig(). The HTTP client is the application's,
 * given to the constructor: without one, nothing that calls the carrier is
 * supported.
 */
final class __Name__Factory extends GatewayFactory
{
    public const BASE_URI = 'https://api.example.org/v1/';

    protected function populateConfig(Config $config): void
    {
        // A factory name is snake_case: a dash of the package's name becomes an underscore here.
        $name = str_replace('-', '_', '__name__');
        $http = $this->http;

        $config->defaults([
            'omnibus.factory_name' => $name,
            'omnibus.factory_title' => '__Title__',
            'omnibus.required_options' => ['api_key'],
            'base_uri' => self::BASE_URI,
            'omnibus.api' => static fn (Config $c) => null === $http ? null : new Api($http, $name, self::string($c, 'api_key'), self::string($c, 'base_uri')),
            'omnibus.action.tracking' => null === $http ? null : new TrackingAction($name),
        ]);
    }

    private static function string(Config $c, string $key): string
    {
        $value = $c->get($key);

        return \is_scalar($value) ? (string) $value : '';
    }
}
