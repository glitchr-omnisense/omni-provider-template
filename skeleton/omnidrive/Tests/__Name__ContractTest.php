<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnidrive\Drive;
use Omnidrive\Testing\DriveContractTests;
use PHPUnit\Framework\TestCase;

/**
 * omnidrive's conformance suite on __Title__. The template's drive keeps its
 * files in memory, so the suite runs offline as it is; once the drive is the
 * service's, give drive() a server of the service's own (in Docker, in the CI's
 * services) or recorded answers, and keepsVisibility() false for a service that
 * keeps no visibility per file.
 */
final class __Name__ContractTest extends TestCase
{
    use DriveContractTests;

    protected function drive(): Drive
    {
        return (new __Name__Factory())->create(['public_url' => 'https://files.example.org']);
    }
}
