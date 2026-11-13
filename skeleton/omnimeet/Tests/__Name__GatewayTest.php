<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__GatewayFactory;
use Omnimeet\Exception\NotSupportedException;
use Omnimeet\Model\Meeting;
use Omnimeet\Registry;
use PHPUnit\Framework\TestCase;

/**
 * The first call, as an application makes it: the gateway built by its
 * factory in omnimeet's Registry, with its policy, and a meeting opened
 * through the Registry's guard refused with omnimeet's
 * NotSupportedException until the gateway has its action. Once it has,
 * replace the last test with the service's answers, recorded (a
 * MockHttpClient's responses).
 */
final class __Name__GatewayTest extends TestCase
{
    public function testTheRegistryBuildsItWithItsPolicy(): void
    {
        $gateway = $this->registry()->get('main');

        self::assertSame(['__name__', '__Title__'], [$gateway->getName(), $gateway->getTitle()]);
        self::assertSame('__name__', $gateway->capabilities()->policy()->provider);
        self::assertSame([], $gateway->capabilities()->operations, 'it answers nothing yet');
    }

    public function testAMeetingIsRefusedUntilTheGatewayHasItsAction(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->registry()->guarded('main')->open(new Meeting('weekly'));
    }

    private function registry(): Registry
    {
        return new Registry([new __Name__GatewayFactory()], ['main' => ['factory' => '__name__']]);
    }
}
