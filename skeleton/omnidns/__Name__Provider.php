<?php

namespace __Namespace__;

use Omnidns\Capabilities;
use Omnidns\Exception\NotSupportedException;
use Omnidns\AvailabilityInterface;
use Omnidns\Model\Availability;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omnidns provider - a skeleton: it declares its usage policy
 * and answers nothing yet. check() says so with omnidns's
 * NotSupportedException, and capabilities() promises nothing.
 *
 * To make it the service's: write check() and set its Capabilities
 * field (availability: true), then add the other contracts the service answers
 * - RegistrarInterface, DomainsInterface, NameserversInterface,
 * DnsInterface - each with its Capabilities field.
 * Fill policy.json in from the service's terms: the family's Registry holds
 * every call to it.
 */
final class __Name__Provider implements AvailabilityInterface, DeclaresUsagePolicyInterface
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

    public function check(string $domain): Availability
    {
        throw NotSupportedException::capability($this, $this->getName(), 'check availability yet (__Name__Provider::check() is not written)');
    }
}
