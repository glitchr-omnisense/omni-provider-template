<?php

namespace __Namespace__;

use __Namespace__\Action\PickupPointsAction;
use __Namespace__\Action\TrackingAction;
use Omnibus\Config;
use Omnibus\GatewayFactory;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, from its options:
 *
 *   client_id      the account's OAuth client (required)
 *   client_secret  its secret (required)
 *   base_uri       the API's address
 *   rates          the shop's prices, in place of a rating API (Omnibus\Action\ConfiguredRatingAction)
 *
 *   (new __Name__Factory($httpClient))->create(['client_id' => '...', 'client_secret' => '...', 'rates' => [...]]);
 *
 * The template's carrier signs in with OAuth's client credentials (Api), tracks
 * a parcel over its API (Action\TrackingAction), lists the pickup points near
 * an address page by page (Action\PickupPointsAction) and prices from the
 * "rates" option; every other request is refused as not supported. To make it the
 * service's, put its address and its authentication in Api (a carrier with a
 * plain key drops token()), add one action per request it answers (Rating,
 * Shipping, Pickup, GetSlip, Cancel, Paperless: omnibus' docs/providers.md)
 * and declare each in populateConfig().
 *
 * The HTTP client is the application's, given to the constructor: without one,
 * nothing that calls the carrier is supported. omnibus' GatewayFactory keeps it
 * with its retries off (Http\NoRetry): a label or a pickup is never sent twice
 * by a retrying client (Tests/NoRetryTest.php).
 *
 * Its usage policy (policy.json) is the gateway's ("omnibus.policy"): the
 * registry's guard counts each family operation ("track", "pickupPoints"...)
 * against it. The requests the gateway makes besides go through guards built
 * here over the same policy, which count in the same limiter: the token
 * request under "token" (Api); each page of pickup points after the first
 * under "pickupPoints" (PickupPointsAction, PolicyGuard::pages()); each
 * parcel's tracking request under "trackParcel", the parcel named, for a
 * limit the carrier sets per parcel (TrackingAction, PolicyGuard::perResource()).
 */
final class __Name__Factory extends GatewayFactory
{
    public const BASE_URI = 'https://api.example.org/v1/';

    /** Its usage policy, as its package declares it (omnisense/contracts' schema): fill it in from the terms. */
    public const POLICY = __DIR__.'/policy.json';

    private static ?UsagePolicy $policy = null;

    protected function populateConfig(Config $config): void
    {
        // A factory name is snake_case: a dash of the package's name becomes an underscore here.
        $name = str_replace('-', '_', '__name__');
        $http = $this->http;

        $config->defaults([
            'omnibus.factory_name' => $name,
            'omnibus.factory_title' => '__Title__',
            'omnibus.required_options' => ['client_id', 'client_secret'],
            'omnibus.policy' => self::usagePolicy(),
            'base_uri' => self::BASE_URI,
            'omnibus.api' => static fn (Config $c) => null === $http ? null : new Api($http, $name, (string) $c->string('client_id'), (string) $c->string('client_secret'), (string) $c->string('base_uri'), $c->backoff(), $c->guard(self::usagePolicy())),
            'omnibus.action.tracking' => null === $http ? null : static fn (Config $c) => new TrackingAction($name, $c->guard(self::usagePolicy())->perResource()),
            'omnibus.action.pickup' => null === $http ? null : static fn (Config $c) => new PickupPointsAction($name, $c->guard(self::usagePolicy())),
        ]);
    }

    /** Its usage policy, as policy.json declares it, read once. */
    public static function usagePolicy(): UsagePolicy
    {
        return self::$policy ??= UsagePolicy::fromFile(self::POLICY);
    }
}
