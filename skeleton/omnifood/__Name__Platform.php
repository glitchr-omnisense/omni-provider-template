<?php

namespace __Namespace__;

use Omnifood\Channel;
use Omnifood\Model\Capabilities;
use Omnifood\PlatformInterface;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omnifood platform - a skeleton: it declares its usage policy
 * and implements none of omnifood's contracts yet, so that the Registry
 * refuses each one (guardedOrders(), guardedMenu(), guardedStore(),
 * guardedReservations()) with omnifood's NotSupportedException.
 *
 * To make it the platform's: implement the contracts it answers, and only
 * those - OrdersInterface, MenuInterface, StoreInterface,
 * ReservationsInterface, NotifiableInterface (its webhooks) - through an
 * Api over the factory's HTTP client and Backoff; say in capabilities()
 * what its orders, menus and reservations carry. getChannel() is the
 * platform omnifood's orders say they come from: a platform that is none
 * of Channel's cases needs its case added to omnifood first (DIRECT, the
 * restaurant's own, stands in until then).
 */
final class __Name__Platform implements PlatformInterface, DeclaresUsagePolicyInterface
{
    /** The usage policy its package declares (omnisense/contracts' schema). */
    public const POLICY = __DIR__.'/policy.json';

    public function getName(): string
    {
        return '__name__';
    }

    public function getChannel(): Channel
    {
        return Channel::DIRECT;
    }

    /** Its usage policy, as its package declares it: policy.json beside this class. */
    public function usagePolicy(): UsagePolicy
    {
        return UsagePolicy::fromFile(self::POLICY);
    }

    public function capabilities(): Capabilities
    {
        return new Capabilities();
    }
}
