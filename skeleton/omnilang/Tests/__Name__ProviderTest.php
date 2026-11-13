<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use __Namespace__\__Name__Provider;
use Omnilang\Exception\NotSupportedException;
use Omnilang\Registry;
use Omnilang\Model\TranslationQuery;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the provider built by its
 * factory in omnilang's Registry, its policy read, and translate() answered
 * with omnilang's NotSupportedException until it is written - through the
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

        $this->registry()->translator('main')->translate(new TranslationQuery('Hello', 'fr'));
    }

    public function testTheProviderSaysItIsNotWrittenYet(): void
    {
        $provider = $this->registry()->get('main');
        self::assertInstanceOf(__Name__Provider::class, $provider);

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('is not written');
        $provider->translate(new TranslationQuery('Hello', 'fr'));
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__Factory()], ['main' => ['factory' => '__name__']]);
    }
}
