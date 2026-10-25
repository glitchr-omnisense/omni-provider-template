<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnitrade\GatewayFactoryInterface;
use Omnitrade\Testing\GatewayContractTests;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * omnitrade's conformance suite on __Title__, offline: the provider's answers
 * are recordedAnswers(), by "METHOD /path". The template's are written in the
 * format its Api reads; replace them with the service's real answers (recorded
 * once from its test mode, or written from its documentation - say which here).
 */
final class __Name__ContractTest extends TestCase
{
    use GatewayContractTests;

    protected function factory(HttpClientInterface $http): GatewayFactoryInterface
    {
        return new __Name__Factory($http);
    }

    protected function options(): array
    {
        return ['secret_key' => 'sk_test_contract'];
    }

    /** @return array<string, array<string, mixed>> */
    protected function recordedAnswers(): array
    {
        return [
            'POST /v1/payments' => ['id' => 'pay_7Hq2', 'status' => 'open', 'amount' => 1250, 'currency' => 'eur', 'checkout_url' => 'https://pay.example.org/checkout/pay_7Hq2'],
            'GET /v1/payments/contract-transaction' => ['id' => 'contract-transaction', 'status' => 'paid', 'amount' => 1250, 'currency' => 'eur'],
        ];
    }
}
