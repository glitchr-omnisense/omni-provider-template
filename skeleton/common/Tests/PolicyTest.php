<?php

namespace __Namespace__\Tests;

use Omnisense\Contracts\Policy\PolicyExport;
use Omnisense\Contracts\Policy\UsagePolicy;
use PHPUnit\Framework\TestCase;

/**
 * __Title__'s usage policy, declared as data: policy.json, named in
 * composer.json's extra.omnisense.policy so that omnisense/contracts' export
 * (vendor/bin/omnisense-policies, the omnisense site's policies table) finds
 * it, and valid against the schema. Once the terms are read, assert their
 * figures here: the limits, the rights, the date they were read.
 */
final class PolicyTest extends TestCase
{
    public function testThePackageDeclaresItsPolicy(): void
    {
        $declared = (new PolicyExport())->folders(\dirname(__DIR__))->entries();
        self::assertCount(1, $declared, 'composer.json\'s extra.omnisense.policy names it');
        self::assertSame(['__package__', 'policy.json'], [$declared[0]['package'], $declared[0]['file']]);
        self::assertArrayNotHasKey('error', $declared[0], 'it reads as the schema says');

        $policy = UsagePolicy::fromFile(\dirname(__DIR__).'/policy.json');
        self::assertSame('__name__', $policy->provider, 'named after the factory');
        self::assertNotNull($policy->provenance->terms, 'it links to the terms it was read from');
    }
}
