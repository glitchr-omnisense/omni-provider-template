<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__CatalogFactory;
use Omnisong\Exception\NotSupportedException;
use Omnisong\Model\Reference;
use Omnisong\Registry;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the catalogue built by its
 * factory in omnisong's Registry, under the policy it declares, and a
 * release asked through the Registry's guard answered with omnisong's
 * NotSupportedException until it is written. Once it is, replace the last
 * tests with the service's answers, recorded (a MockHttpClient's
 * responses).
 */
final class __Name__CatalogTest extends TestCase
{
    public function testTheRegistryBuildsItUnderItsPolicy(): void
    {
        $registry = $this->registry();

        self::assertSame('__name__', $registry->get('main')->getName());
        self::assertSame('__name__', $registry->policy('main')->provider, 'policy.json, declared by the catalogue');
    }

    public function testAReleaseSaysItIsNotWrittenYet(): void
    {
        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('is not written');

        $this->registry()->guarded('main')->release(Reference::isrc('USRC17607839'));
    }

    public function testAnArtistsReleasesSayTheyAreNotWrittenYet(): void
    {
        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('is not written');

        $this->registry()->guarded('main')->releases('Example');
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__CatalogFactory()], ['main' => ['factory' => '__name__']]);
    }
}
