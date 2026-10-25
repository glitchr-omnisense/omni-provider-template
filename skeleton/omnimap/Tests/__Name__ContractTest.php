<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnimap\ProviderFactoryInterface;
use Omnimap\Testing\ArrayCache;
use Omnimap\Testing\ProviderContractTests;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/** omnimap's conformance suite on __Title__, offline: the tiles from the suite's recorded PNG. */
final class __Name__ContractTest extends TestCase
{
    use ProviderContractTests;

    protected function contractFactory(?HttpClientInterface $http): ProviderFactoryInterface
    {
        return new __Name__Factory($http, new ArrayCache());
    }

    /** @return array<string, mixed> */
    protected function contractOptions(): array
    {
        return ['tiles' => 'https://tiles.example.org/{z}/{x}/{y}.png', 'attribution' => '&copy; Example', 'user_agent' => 'contract-tests/1.0'];
    }
}
