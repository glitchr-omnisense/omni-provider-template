<?php

namespace __Namespace__;

use Omnipost\Exception\InvalidConfigException;
use Omnipost\Exception\NotSupportedException;
use Omnipost\FeedInterface;
use Omnipost\Model\Account;
use Omnipost\Model\Feed;
use Omnipost\Platform;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omnipost provider - a skeleton: it declares its usage policy
 * and answers nothing yet. account() and feed() say so with omnipost's
 * NotSupportedException.
 *
 * To make it the network's: write account() and feed(), and implement
 * PublisherInterface if it publishes (publish(), status(), delete(), and
 * the capabilities() its posts must meet), through an Api over the
 * factory's HTTP client and Backoff. getPlatform() is the network among
 * omnipost's Platform cases named like the factory ("__name__"): a network
 * none of them names needs its case added to omnipost first.
 */
final class __Name__Provider implements FeedInterface, DeclaresUsagePolicyInterface
{
    /** The usage policy its package declares (omnisense/contracts' schema). */
    public const POLICY = __DIR__.'/policy.json';

    public function getName(): string
    {
        return '__name__';
    }

    public function getPlatform(): Platform
    {
        return Platform::tryFrom($this->getName()) ?? throw new InvalidConfigException(\sprintf('omnipost knows no "%s" network: add its case to Omnipost\Platform first.', $this->getName()));
    }

    /** Its usage policy, as its package declares it: policy.json beside this class. */
    public function usagePolicy(): UsagePolicy
    {
        return UsagePolicy::fromFile(self::POLICY);
    }

    public function account(): Account
    {
        throw NotSupportedException::operation($this->getName(), 'give its account yet (__Name__Provider::account() is not written)');
    }

    public function feed(?string $cursor = null, int $limit = 25): Feed
    {
        throw NotSupportedException::operation($this->getName(), 'give its feed yet (__Name__Provider::feed() is not written)');
    }
}
