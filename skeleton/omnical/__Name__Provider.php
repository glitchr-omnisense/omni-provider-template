<?php

namespace __Namespace__;

use Omnical\Capabilities;
use Omnical\Exception\NotSupportedException;
use Omnical\HolidaysInterface;
use Omnical\Model\HolidayQuery;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omnical provider - a skeleton: it declares its usage policy
 * and answers nothing yet. holidays() says so with omnical's
 * NotSupportedException, and capabilities() promises nothing.
 *
 * To make it the service's: write holidays() and set its Capabilities
 * field (holidays: true, the countries it covers), then add the other contracts the service answers
 * - CalendarEventsInterface, FreeBusyInterface - each with its
 * Capabilities field. A keyless JSON API may need no class at all: omnical
 * reads a declarative provider.json (omnical/nager).
 * Fill policy.json in from the service's terms: the family's Registry holds
 * every call to it.
 */
final class __Name__Provider implements HolidaysInterface, DeclaresUsagePolicyInterface
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

    public function holidays(HolidayQuery $query): array
    {
        throw NotSupportedException::capability($this, $this->getName(), 'give holidays yet (__Name__Provider::holidays() is not written)');
    }
}
