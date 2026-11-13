<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use __Namespace__\__Name__Provider;
use Omnipulse\Exception\NotSupportedException;
use Omnipulse\Model\ReachQuery;
use Omnipulse\Registry;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the provider built by its
 * factory in omnipulse's Registry, its policy read, and the audience asked
 * answered with omnipulse's NotSupportedException until audience() is
 * written. Once it is, replace the last tests with recorded answers
 * (omnipulse's Testing\RecordedHttpClient) and the service's figures.
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

    public function testTheRegistryRefusesWhatItDoesNotGiveYet(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->audience('main');
    }

    public function testTheAudienceSaysItIsNotWrittenYet(): void
    {
        $provider = $this->registry()->get('main');
        self::assertInstanceOf(__Name__Provider::class, $provider);

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('is not written');
        $provider->audience(new ReachQuery(new \DateTimeImmutable('-7 days'), new \DateTimeImmutable()));
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__Factory()], ['main' => ['factory' => '__name__']]);
    }
}
