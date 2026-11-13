<?php

namespace __Namespace__;

use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;
use Omnishow\Capabilities;
use Omnishow\Exception\NotSupportedException;
use Omnishow\Model\Query;
use Omnishow\ProviderInterface;
use Omnishow\SearchInterface;

/**
 * __Title__, an omnishow provider - a skeleton: it declares its usage policy
 * and answers nothing yet. search() says so with omnishow's
 * NotSupportedException, and capabilities() promises nothing.
 *
 * To make it the service's: write search() (works whose title matches, as
 * omnishow's Model\Work), set "search: true" and the types it knows in
 * capabilities(), then add the other contracts the service answers -
 * LookupInterface (a work by an identifier), EpisodesInterface,
 * AvailabilityInterface, VideoInterface, VideoSearchInterface,
 * ChannelInterface, PlaylistInterface, LiveInterface - each with its
 * Capabilities field. Fill policy.json in from the service's terms: the
 * family's Registry holds every call to it.
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

    public function search(Query $query): array
    {
        throw NotSupportedException::capability($this, $this->getName(), 'search by title yet (__Name__Provider::search() is not written)');
    }
}
