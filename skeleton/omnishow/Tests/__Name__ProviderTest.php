<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnishow\Exception\NotSupportedException;
use Omnishow\Model\Query;
use Omnishow\Registry;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the provider built by its
 * factory in omnishow's Registry, its policy read, and a search through the
 * Registry's guard answered with omnishow's NotSupportedException until
 * search() is written. Once it is, replace the last test with recorded
 * answers (omnishow's Testing\RecordedHttpClient) and the service's fields.
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

    public function testASearchSaysItIsNotWrittenYet(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->search('main')->search(new Query('Stalker'));
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__Factory()], ['main' => ['factory' => '__name__']]);
    }
}
