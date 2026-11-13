<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use __Namespace__\__Name__Provider;
use Omnitravel\Exception\NotSupportedException;
use Omnitravel\Registry;
use Omnitravel\Model\DepartureQuery;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the provider built by its
 * factory in omnitravel's Registry, its policy read, and departures() answered
 * with omnitravel's NotSupportedException until it is written - through the
 * Registry as by the provider itself. Once it is, replace the last tests
 * with the service's answers, recorded.
 */
final class __Name__ProviderTest extends TestCase
{
    public function testTheRegistryBuildsItWithItsPolicy(): void
    {
        $provider = $this->registry()->get('main');

        self::assertSame(['__name__', '__Title__'], [$provider->getName(), $provider->getTitle()]);
        self::assertSame('__name__', $provider->capabilities()->policy()->provider);
        self::assertSame([], $provider->capabilities()->contracts(), 'it promises nothing yet');
    }

    public function testTheRegistrySaysItIsNotWrittenYet(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->timetables('main')->departures(new DepartureQuery('stop:1'));
    }

    public function testTheProviderSaysItIsNotWrittenYet(): void
    {
        $provider = $this->registry()->get('main');
        self::assertInstanceOf(__Name__Provider::class, $provider);

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('is not written');
        $provider->departures(new DepartureQuery('stop:1'));
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__Factory()], ['main' => ['factory' => '__name__']]);
    }
}
