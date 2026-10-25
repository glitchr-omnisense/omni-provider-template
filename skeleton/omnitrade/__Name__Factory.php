<?php

namespace __Namespace__;

use __Namespace__\Action\FetchTransactionAction;
use __Namespace__\Action\PurchaseAction;
use Omnitrade\Config;
use Omnitrade\GatewayFactory;

/**
 * __Title__, from its options:
 *
 *   secret_key  the account's secret key, sent as a bearer token (required)
 *   base_uri    the API's address
 *
 *   (new __Name__Factory($httpClient))->create(['secret_key' => '...']);
 *
 * The template's provider takes a payment on its hosted page
 * (Action\PurchaseAction) and reads where a payment stands
 * (Action\FetchTransactionAction), through Api; every other request is refused
 * as not supported. To make it the service's, put its address and its
 * authentication in Api, add one action per request it answers (Authorize,
 * Capture, Refund, Notify...: omnitrade's docs/providers.md) and declare each in
 * populateConfig(). The HTTP client is the application's, given to the
 * constructor: without one, nothing is supported.
 */
final class __Name__Factory extends GatewayFactory
{
    public const BASE_URI = 'https://api.example.org/v1/';

    protected function populateConfig(Config $config): void
    {
        $http = $this->http;

        $config->defaults([
            'omnitrade.factory_name' => '__name__',
            'omnitrade.factory_title' => '__Title__',
            'omnitrade.required_options' => ['secret_key'],
            'base_uri' => self::BASE_URI,
            'omnitrade.api' => static fn (Config $c) => null === $http ? null : new Api($http, '__name__', self::string($c, 'secret_key'), self::string($c, 'base_uri')),
            'omnitrade.action.purchase' => null === $http ? null : new PurchaseAction(),
            'omnitrade.action.fetch_transaction' => null === $http ? null : new FetchTransactionAction(),
        ]);
    }

    private static function string(Config $c, string $key): string
    {
        $value = $c->get($key);

        return \is_scalar($value) ? (string) $value : '';
    }
}
