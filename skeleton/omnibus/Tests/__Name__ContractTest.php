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
 * which here), one route per call.
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
            'api_key' => 'test-key',
            'rates' => [
                ['service' => 'standard', 'label' => 'Standard', 'days' => 3, 'countries' => ['FR'], 'bands' => [1000 => 690, 5000 => 990]],
                ['service' => 'express', 'label' => 'Express', 'days' => 1, 'countries' => ['FR'], 'bands' => [1000 => 1290]],
            ],
        ];
    }

    protected function recordedAnswers(): array
    {
        return [
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
}
