<?php

namespace __Namespace__;

use Omniticket\Capabilities;
use Omniticket\Exception\NotSupportedException;
use Omniticket\EventCatalogInterface;
use Omniticket\Model\Event;
use Omniticket\Model\EventQuery;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omniticket provider - a skeleton: it declares its usage policy
 * and answers nothing yet. event() says so with omniticket's
 * NotSupportedException, and capabilities() promises nothing.
 *
 * To make it the service's: write event() and set its Capabilities
 * field (catalog: true), then add the other contracts the service answers
 * - AttendeeListInterface, TicketCheckInterface - each with its
 * Capabilities field.
 * Fill policy.json in from the service's terms: the family's Registry holds
 * every call to it.
 */
final class __Name__Provider implements EventCatalogInterface, DeclaresUsagePolicyInterface
{
    /** The usage policy its package declares (omnisense/contracts' schema). */
    public const POLICY = __DIR__.'/policy.json';

    public function getName(): string
    {
        return '__name__';
    }

    public function getTitle(): string
    {
        return '__Title__';
    }

    /** Its usage policy, as its package declares it: policy.json beside this class. */
    public function usagePolicy(): UsagePolicy
    {
        return UsagePolicy::fromFile(self::POLICY);
    }

    public function capabilities(): Capabilities
    {
        return new Capabilities(policy: $this->usagePolicy());
    }

    public function events(EventQuery $query = new EventQuery()): iterable
    {
        throw NotSupportedException::capability($this, $this->getName(), 'list its events yet (__Name__Provider::events() is not written)');
    }

    public function event(string $id): Event
    {
        throw NotSupportedException::capability($this, $this->getName(), 'give an event yet (__Name__Provider::event() is not written)');
    }
}
