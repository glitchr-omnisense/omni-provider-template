<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__SourceFactory;
use Omnilex\Exception\InvalidConfigException;
use Omnilex\Exception\NotSupportedException;
use Omnilex\Model\Capabilities;
use Omnilex\Registry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;

/**
 * The first call, as an application makes it: the source built by its
 * factory in omnilex's Registry, under the policy its package declares,
 * and each capability refused with omnilex's NotSupportedException - with
 * no request sent - until the source implements it. Once it does, replace
 * the last tests with the service's answers, recorded (a MockHttpClient's
 * responses).
 */
final class __Name__SourceTest extends TestCase
{
    /** @var list<string> */
    private array $sent = [];

    public function testTheRegistryBuildsItUnderItsPolicy(): void
    {
        $registry = $this->registry();

        self::assertSame('__name__', $registry->get('main')->getName());
        self::assertSame([], Capabilities::operations($registry->get('main')), 'it does nothing yet');
        self::assertSame('__name__', $registry->policy('main')->provider, 'policy.json, named in composer.json');
    }

    public function testATextIsRefusedUntilWritten(): void
    {
        try {
            $this->registry()->texts('main');
            self::fail('The source read texts.');
        } catch (NotSupportedException) {
        }
        self::assertSame([], $this->sent, 'nothing was sent');
    }

    public function testASearchIsRefusedUntilWritten(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->search('main');
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
