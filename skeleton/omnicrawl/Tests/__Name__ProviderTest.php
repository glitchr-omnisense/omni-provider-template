<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use __Namespace__\__Name__Provider;
use Omnicrawl\Agent;
use Omnicrawl\Exception\NotSupportedException;
use Omnicrawl\Guard\Gate;
use Omnicrawl\Model\FetchRequest;
use Omnicrawl\Registry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * The first call, as an application makes it: the provider built by its
 * factory in omnicrawl's Registry, under the Gate's identity, its policy
 * read, and fetch() answered with omnicrawl's NotSupportedException until it
 * is written. The Gate's own requests (robots.txt...) are answered 404 here:
 * nothing leaves the test. Once fetch() is written, replace the last test
 * with the service's answers, recorded.
 */
final class __Name__ProviderTest extends TestCase
{
    private const AGENT = 'ExampleBot/1.0 (+https://example.org/bot; bot@example.org)';

    public function testTheRegistryBuildsItWithItsPolicy(): void
    {
        $provider = $this->registry()->get('main');

        self::assertSame(['__name__', '__Title__'], [$provider->getName(), $provider->getTitle()]);
        self::assertSame('__name__', $provider->capabilities()->policy()->provider);
        self::assertSame([], $provider->capabilities()->contracts(), 'it promises nothing yet');
    }

    public function testThroughTheGateTooItSaysItIsNotWrittenYet(): void
    {
        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('is not written');

        $this->registry()->pageFetch('main')->fetch('https://example.org/');
    }

    public function testTheProviderSaysItIsNotWrittenYet(): void
    {
        $provider = $this->registry()->get('main');
        self::assertInstanceOf(__Name__Provider::class, $provider);

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('is not written');
        $provider->fetch(new FetchRequest('https://example.org/', Agent::parse(self::AGENT)));
    }

    private function registry(): Registry
    {
        $gate = new Gate(Agent::parse(self::AGENT), new MockHttpClient(static fn () => new MockResponse('', ['http_code' => 404])));

        return new Registry([new __Name__Factory()], ['main' => ['factory' => '__name__']], $gate);
    }
}
