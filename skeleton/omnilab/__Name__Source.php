<?php

namespace __Namespace__;

use Omnilab\Exception\NotSupportedException;
use Omnilab\Model\Entity;
use Omnilab\Model\Identifier;
use Omnilab\Source\HttpSource;

/**
 * __Title__, an omnilab source - a skeleton: it declares its usage policy
 * (policy.json, read by HttpSource from composer.json's
 * extra.omnisense.policy) and answers nothing yet. entity() says so, and
 * every other operation is HttpSource's NotSupportedException;
 * capabilities() promises nothing.
 *
 * To make it the service's: override the operations it answers - entity(),
 * search(), series(), products(), occurrences(), datasets(), open() - with
 * HttpSource's calls (getJson(), postForJson()...: every request goes
 * through the source's usage policy, its errors mapped as the family says),
 * and list each one in capabilities() (Source\Capability).
 */
final class __Name__Source extends HttpSource
{
    public const BASE_URI = 'https://api.example.org/';

    public function getName(): string
    {
        return '__name__';
    }

    public function capabilities(): array
    {
        return [];
    }

    public function entity(Identifier|string $id): ?Entity
    {
        throw NotSupportedException::operation($this->getName(), 'look up entities yet (__Name__Source::entity() is not written)');
    }
}
