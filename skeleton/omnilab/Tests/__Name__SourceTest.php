<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__SourceFactory;
use Omnilab\Exception\InvalidConfigException;
use Omnilab\Exception\NotSupportedException;
use Omnilab\Model\Point;
use Omnilab\Registry;
use Omnilab\Source\SeriesQuery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;

/**
 * The first call, as an application makes it: the source built by its
 * factory in omnilab's Registry, under the policy its package declares, and
 * each operation answered with omnilab's NotSupportedException - with no
 * request sent - until it is written. Once it is, replace the last tests
 * with the service's answers, recorded (a MockHttpClient's responses).
 */
final class __Name__SourceTest extends TestCase
{
    /** @var list<string> */
    private array $sent = [];

    public function testTheRegistryBuildsItUnderItsPolicy(): void
    {
        $registry = $this->registry();

        self::assertSame('__name__', $registry->get('main')->getName());
        self::assertSame([], $registry->get('main')->capabilities(), 'it promises nothing yet');
        self::assertSame('__name__', $registry->policy('main')->provider, 'policy.json, named in composer.json');
    }

    public function testAnEntitySaysItIsNotWrittenYet(): void
    {
        try {
            $this->registry()->get('main')->entity('42');
            self::fail('An entity was looked up.');
        } catch (NotSupportedException) {
        }
        self::assertSame([], $this->sent, 'nothing was sent');
    }

    public function testWhatItDoesNotOverrideIsRefused(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->get('main')->series(new SeriesQuery(['temperature'], new Point(48.58, 7.75)));
    }

    public function testItNeedsTheApplicationsHttpClient(): void
    {
        $this->expectException(InvalidConfigException::class);

        (new __Name__SourceFactory())->create();
    }

    private function registry(): Registry
    {
        $http = new MockHttpClient(function (string $method, string $url) {
            $this->sent[] = $method.' '.$url;
            throw new \LogicException('No request is expected.');
        });

        return new Registry([new __Name__SourceFactory($http)], ['main' => ['factory' => '__name__']]);
    }
}
