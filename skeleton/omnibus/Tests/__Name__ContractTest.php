<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnibus\GatewayFactoryInterface;
use Omnibus\Testing\GatewayContractTests;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * omnibus' conformance suite on __Title__, offline: the carrier's answers are
 * recordedAnswers(). The template's are written in the format its
 * Action\TrackingAction reads; replace them with the service's real answers
 * (recorded once from its sandbox, or written from its documentation - say
 * which here), one route per call; sessionRoutes() names the token request,
 * which keeps answering while the suite fails the others.
 */
final class __Name__ContractTest extends TestCase
{
    use GatewayContractTests;

    protected function gatewayFactory(HttpClientInterface $http): GatewayFactoryInterface
    {
        return new __Name__Factory($http);
    }

    protected function gatewayOptions(): array
    {
        return [
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
            'rates' => [
                ['service' => 'standard', 'label' => 'Standard', 'days' => 3, 'countries' => ['FR'], 'bands' => [1000 => 690, 5000 => 990]],
                ['service' => 'express', 'label' => 'Express', 'days' => 1, 'countries' => ['FR'], 'bands' => [1000 => 1290]],
            ],
        ];
    }

    protected function recordedAnswers(): array
    {
        return [
            'POST /v1/oauth/token' => ['access_token' => 'recorded-token', 'token_type' => 'Bearer', 'expires_in' => 3600],
            // Two pages: the suite asks two points, one on each.
            'GET /v1/pickup-points?page=1' => ['points' => [['id' => 'PP-001', 'name' => 'Tabac du Marché', 'street' => '12 rue Oberkampf', 'postcode' => '75011', 'city' => 'Paris', 'country' => 'FR', 'latitude' => 48.8649, 'longitude' => 2.3725, 'distance' => 120]], 'next_page' => 2],
            'GET /v1/pickup-points?page=2' => ['points' => [['id' => 'PP-002', 'name' => 'Librairie Folie', 'street' => '3 rue de la Folie-Méricourt', 'postcode' => '75011', 'city' => 'Paris', 'country' => 'FR', 'latitude' => 48.8641, 'longitude' => 2.3701, 'distance' => 340]], 'next_page' => 3],
            'GET /v1/tracking/1234567890' => [
                'tracking_number' => '1234567890',
                'status' => 'out_for_delivery',
                'events' => [
                    ['at' => '2026-10-21T07:40:00+02:00', 'status' => 'out_for_delivery', 'description' => 'Out for delivery', 'location' => 'Paris 11', 'code' => 'OFD'],
                    ['at' => '2026-10-20T08:12:00+02:00', 'status' => 'pending', 'description' => 'Label created', 'location' => 'Paris', 'code' => 'LBL'],
                    ['at' => '2026-10-20T18:30:00+02:00', 'status' => 'in_transit', 'description' => 'Parcel picked up', 'location' => 'Paris', 'code' => 'PUP'],
                ],
            ],
        ];
    }

    /** @return list<string> */
    protected function sessionRoutes(): array
    {
        return ['POST /v1/oauth/token'];
    }
}
