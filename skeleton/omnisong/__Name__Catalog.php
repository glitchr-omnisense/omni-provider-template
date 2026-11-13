<?php

namespace __Namespace__;

use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;
use Omnisong\Catalog\CatalogInterface;
use Omnisong\Exception\NotSupportedException;
use Omnisong\Model\PlatformLinks;
use Omnisong\Model\Reference;
use Omnisong\Model\Release;

/**
 * __Title__, an omnisong catalogue - a skeleton: it declares its usage
 * policy and answers nothing yet. links(), release() and releases() say so
 * with omnisong's NotSupportedException.
 *
 * To make it the service's: write what it answers - links() (a release's
 * pages on every platform), release() (a release by its URL, ISRC or UPC),
 * releases() (an artist's) - and keep NotSupportedException::operation()
 * or ::reference() for the rest. Fill policy.json in from the service's
 * terms: the Registry's guard holds every call to it.
 */
final class __Name__Catalog implements CatalogInterface, DeclaresUsagePolicyInterface
{
    /** The usage policy its package declares (omnisense/contracts' schema). */
    public const POLICY = __DIR__.'/policy.json';

    public function getName(): string
    {
        return '__name__';
    }

    /** Its usage policy, as its package declares it: policy.json beside this class. */
    public function usagePolicy(): UsagePolicy
    {
        return UsagePolicy::fromFile(self::POLICY);
    }

    public function links(Reference $reference): PlatformLinks
    {
        throw NotSupportedException::operation($this->getName(), 'give links yet (__Name__Catalog::links() is not written)');
    }

    public function release(Reference $reference): ?Release
    {
        throw NotSupportedException::operation($this->getName(), 'give a release yet (__Name__Catalog::release() is not written)');
    }

    public function releases(string $artist, int $limit = 50, ?string $country = null): array
    {
        throw NotSupportedException::operation($this->getName(), 'list releases yet (__Name__Catalog::releases() is not written)');
    }
}
