<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__ProviderFactory;
use Omnipost\Exception\InvalidConfigException;
use Omnipost\Exception\NotSupportedException;
use Omnipost\Registry;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the provider built by its
 * factory in omnipost's Registry, under the policy it declares, and its
 * feed answered with omnipost's NotSupportedException until it is written.
 * Once it is, replace the last tests with the network's answers, recorded
 * (a MockHttpClient's responses).
 */
final class __Name__ProviderTest extends TestCase
{
    public function testTheRegistryBuildsItUnderItsPolicy(): void
    {
        $registry = $this->registry();

        self::assertSame('__name__', $registry->get('main')->getName());
        self::assertSame('__name__', $registry->policy('main')->provider, 'policy.json, declared by the provider');
        self::assertSame([], $registry->publishers(), 'it publishes nothing yet');
    }

    public function testTheFeedSaysItIsNotWrittenYet(): void
    {
        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('is not written');

        $this->registry()->feed('main')->feed();
    }

    public function testPublishingIsRefused(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->registry()->publisher('main');
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__ProviderFactory()], ['main' => ['factory' => '__name__']]);
    }
}
