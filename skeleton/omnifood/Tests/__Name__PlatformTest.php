<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__PlatformFactory;
use Omnifood\Exception\NotSupportedException;
use Omnifood\Registry;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the platform built by its
 * factory in omnifood's Registry, under the policy it declares, and each
 * contract refused with omnifood's NotSupportedException until the
 * platform implements it. Once it does, replace the last tests with the
 * platform's answers, recorded (a MockHttpClient's responses).
 */
final class __Name__PlatformTest extends TestCase
{
    public function testTheRegistryBuildsItUnderItsPolicy(): void
    {
        $registry = $this->registry();

        self::assertSame('__name__', $registry->get('main')->getName());
        self::assertSame('__name__', $registry->policy('main')->provider, 'policy.json, declared by the platform');
        self::assertSame([], $registry->stores(), 'it is no store yet');
    }

    public function testTheStoreIsRefusedUntilWritten(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->guardedStore('main');
    }

    public function testOrdersAreRefusedUntilWritten(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->guardedOrders('main');
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__PlatformFactory()], ['main' => ['factory' => '__name__']]);
    }
}
