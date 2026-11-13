<?php

namespace __Namespace__;

use Omnitravel\Capabilities;
use Omnitravel\Exception\NotSupportedException;
use Omnitravel\Model\DepartureQuery;
use Omnitravel\TimetablesInterface;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omnitravel provider - a skeleton: it declares its usage policy
 * and answers nothing yet. departures() says so with omnitravel's
 * NotSupportedException, and capabilities() promises nothing.
 *
 * To make it the service's: write departures() and set its Capabilities
 * field (timetables: true), then add the other contracts the service answers
 * - ItinerariesInterface (journeys), TrackingInterface (vehicles),
 * NetworkInterface (stops, lines) - each with its Capabilities field.
 * Fill policy.json in from the service's terms: the family's Registry holds
 * every call to it.
 */
final class __Name__Provider implements TimetablesInterface, DeclaresUsagePolicyInterface
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

    public function departures(DepartureQuery $query): array
    {
        throw NotSupportedException::capability($this, $this->getName(), 'give departures yet (__Name__Provider::departures() is not written)');
    }
}
