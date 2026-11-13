<?php

namespace __Namespace__;

use Omnistay\Capabilities;
use Omnistay\Exception\NotSupportedException;
use Omnistay\Model\Page;
use Omnistay\Model\StayQuery;
use Omnistay\ProviderInterface;
use Omnistay\SearchInterface;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omnistay provider - a skeleton: it declares its usage policy
 * and answers nothing yet. search() says so with omnistay's
 * NotSupportedException, and capabilities() promises nothing.
 *
 * To make it the service's: write search() and set its Capabilities
 * field (search: true), then add the other contracts the service answers
 * - AvailabilityInterface, CalendarInterface, BookInterface,
 * CancelInterface, ContentInterface - each with its Capabilities field.
 * Fill policy.json in from the service's terms: the family's Registry holds
 * every call to it.
 */
final class __Name__Provider implements ProviderInterface, SearchInterface, DeclaresUsagePolicyInterface
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

    public function search(StayQuery $query): Page
    {
        throw NotSupportedException::capability($this, $this->getName(), 'search stays yet (__Name__Provider::search() is not written)');
    }
}
