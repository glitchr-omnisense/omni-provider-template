<?php

namespace __Namespace__;

use Omnicrawl\Capabilities;
use Omnicrawl\Exception\NotSupportedException;
use Omnicrawl\FetcherInterface;
use Omnicrawl\Model\FetchRequest;
use Omnicrawl\Model\Fetched;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omnicrawl provider - a skeleton: it declares its usage policy
 * and answers nothing yet. fetch() says so with omnicrawl's
 * NotSupportedException, and capabilities() promises nothing.
 *
 * To make it the service's: write fetch() and set its Capabilities
 * field (pageFetch: true; rendering when it renders), then CrawlRunsInterface
 * if the service crawls - with its field. It answers with what the target
 * served (Model\Fetched): the Registry's Gate admits each call first (the
 * targets, robots.txt, the host's limits) and makes the record.
 * Fill policy.json in from the service's terms: the family's Registry holds
 * every call to it.
 */
final class __Name__Provider implements FetcherInterface, DeclaresUsagePolicyInterface
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

    public function fetch(FetchRequest $request): Fetched
    {
        throw NotSupportedException::capability($this, $this->getName(), 'fetch pages yet (__Name__Provider::fetch() is not written)');
    }
}
