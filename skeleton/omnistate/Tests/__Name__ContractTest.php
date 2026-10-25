<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__;
use Omnistate\Testing\RegistryContractTests;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * omnistate's conformance suite on __Title__, offline: the register's answers
 * are recordedAnswers(), by "METHOD /path". The template's are written in the
 * format __Name__ reads; replace them with the service's real answers
 * (recorded once, or written from its documentation - say which here), and
 * companyIdentifier() and searchQuery() with what they know.
 */
final class __Name__ContractTest extends TestCase
{
    use RegistryContractTests;

    protected function registry(HttpClientInterface $http): object
    {
        return new __Name__($http, 'test-key');
    }

    /** @return array<string, array<string, mixed>> */
    protected function recordedAnswers(): array
    {
        $company = ['id' => '901821074', 'name' => 'GLITCH ART', 'status' => 'active', 'country' => 'FR'];

        return [
            'GET /api/companies/901821074' => $company,
            'GET /api/companies' => ['results' => [$company, ['id' => '901821075', 'name' => 'GLITCH STUDIO', 'status' => 'closed', 'country' => 'FR']]],
        ];
    }
}
