<?php

namespace __Namespace__;

use Omnipulse\AudienceInterface;
use Omnipulse\Capabilities;
use Omnipulse\Exception\NotSupportedException;
use Omnipulse\Model\ReachQuery;
use Omnipulse\Model\Report;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omnipulse provider - a skeleton: it declares its usage
 * policy and answers nothing yet. audience() says so with omnipulse's
 * NotSupportedException, and capabilities() promises nothing.
 *
 * To make it the service's: keep the contracts it answers and drop the
 * others - AudienceInterface or SearchPerformanceInterface (a site's
 * audience), RatingInterface, ReviewsInterface, ReplyInterface,
 * InviteInterface, WriteUrlInterface (an establishment's reviews, with
 * TermsInterface's terms()), ProfileInterface, SponsorsInterface,
 * TiersInterface (a project's funding) - each with its Capabilities field
 * ("audience: true" and the dimensions and metrics it reports). Fill
 * policy.json in from the service's terms: the family's Registry holds
 * every call to it.
 */
final class __Name__Provider implements AudienceInterface, DeclaresUsagePolicyInterface
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

    public function audience(ReachQuery $query): Report
    {
        throw NotSupportedException::capability($this, $this->getName(), 'report the audience yet (__Name__Provider::audience() is not written)');
    }
}
