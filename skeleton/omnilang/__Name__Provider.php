<?php

namespace __Namespace__;

use Omnilang\Capabilities;
use Omnilang\Exception\NotSupportedException;
use Omnilang\Model\TranslationQuery;
use Omnilang\TranslatorInterface;
use Omnisense\Contracts\Policy\DeclaresUsagePolicyInterface;
use Omnisense\Contracts\Policy\UsagePolicy;

/**
 * __Title__, an omnilang provider - a skeleton: it declares its usage policy
 * and answers nothing yet. translate() says so with omnilang's
 * NotSupportedException, and capabilities() promises nothing.
 *
 * To make it the service's: write translate() and set its Capabilities
 * field (translate: true), then add the other contracts the service answers
 * - DetectorInterface, GlossaryInterface, DocumentsInterface - each
 * with its Capabilities field.
 * Fill policy.json in from the service's terms: the family's Registry holds
 * every call to it.
 */
final class __Name__Provider implements TranslatorInterface, DeclaresUsagePolicyInterface
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
        return new Capabilities($this->usagePolicy());
    }

    public function languages(): array
    {
        throw NotSupportedException::capability($this, $this->getName(), 'list its languages yet (__Name__Provider::languages() is not written)');
    }

    public function translate(TranslationQuery $query): array
    {
        throw NotSupportedException::capability($this, $this->getName(), 'translate yet (__Name__Provider::translate() is not written)');
    }
}
