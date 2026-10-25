<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnisearch\DBAL\DriverFactory;
use Omnisearch\Testing\ContractDocument;
use Omnisearch\Testing\DriverContractTests;
use PHPUnit\Framework\TestCase;

/**
 * omnisearch's conformance suite on __Title__. The template's driver works in
 * the PHP process, so the suite runs as it is; once it is the engine's, give
 * options() a server of the engine's own (in Docker, in the CI's services) with
 * a random prefix, requiredOptions() its required options and
 * unreachableOptions() an address where nothing answers.
 */
final class __Name__ContractTest extends TestCase
{
    use DriverContractTests;

    protected function factory(): DriverFactory
    {
        return new __Name__Factory();
    }

    protected function options(): array
    {
        return [];
    }

    protected function document(string $id, string $title, int $rank, array $labels): object
    {
        return new ContractDocument($id, $title, $rank, $labels);
    }
}
