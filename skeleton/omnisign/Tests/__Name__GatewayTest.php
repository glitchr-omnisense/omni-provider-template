<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__GatewayFactory;
use Omnisign\Exception\NotSupportedException;
use Omnisign\Model\Envelope;
use Omnisign\Registry;
use Omnisign\Request\Create;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the gateway built by its
 * factory in omnisign's Registry, with its policy, and an envelope's
 * creation refused with omnisign's NotSupportedException until the gateway
 * has its CreateAction. Once it has, replace the last test with the
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
        self::assertFalse($gateway->supports(Create::class));
    }

    public function testAnEnvelopeIsRefusedUntilTheGatewayHasItsAction(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->get('main')->create(new Envelope('Lease'));
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__GatewayFactory()], ['main' => ['factory' => '__name__']]);
    }
}
