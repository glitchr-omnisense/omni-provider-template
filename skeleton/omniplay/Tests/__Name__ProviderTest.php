<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use __Namespace__\__Name__Provider;
use Omniplay\Exception\NotSupportedException;
use Omniplay\Registry;
use Omniplay\Model\Query;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the provider built by its
 * factory in omniplay's Registry, its policy read, and search() answered
 * with omniplay's NotSupportedException until it is written - through the
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

        $this->registry()->search('main')->search(new Query('Portal'));
    }

    public function testTheProviderSaysItIsNotWrittenYet(): void
    {
        $provider = $this->registry()->get('main');
        self::assertInstanceOf(__Name__Provider::class, $provider);

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('is not written');
        $provider->search(new Query('Portal'));
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__Factory()], ['main' => ['factory' => '__name__']]);
    }
}
