<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__GatewayFactory;
use Omnibill\Exception\NotSupportedException;
use Omnibill\Registry;
use Omnibill\Request\Submit;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the gateway built by its
 * factory in omnibill's Registry, with its policy, and an inbox
 * asked refused with omnibill's NotSupportedException until the gateway
 * has its ReceiveAction. Once it has, replace the last test with the
 * service's answers, recorded (a MockHttpClient's responses).
 */
final class __Name__GatewayTest extends TestCase
{
    public function testTheRegistryBuildsItWithItsPolicy(): void
    {
        $gateway = $this->registry()->get('main');

        self::assertSame(['__name__', '__Title__'], [$gateway->getName(), $gateway->getTitle()]);
        self::assertSame('__name__', $gateway->capabilities()->policy()->provider);
        self::assertSame([], $gateway->capabilities()->contracts, 'it promises nothing yet');
        self::assertFalse($gateway->supports(Submit::class));
    }

    public function testTheInboxIsRefusedUntilTheGatewayHasItsAction(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->get('main')->receive();
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__GatewayFactory()], ['main' => ['factory' => '__name__']]);
    }
}
