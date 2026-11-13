<?php

namespace __Namespace__;

use Omnischolar\Exception\NotSupportedException;
use Omnischolar\Model\Work;
use Omnischolar\Model\Identifier;
use Omnischolar\Source\HttpSource;

/**
 * __Title__, an omnischolar source - a skeleton: it declares its usage policy
 * (policy.json, read by HttpSource from composer.json's
 * extra.omnisense.policy) and answers nothing yet. work() says so, and
 * every other operation is HttpSource's NotSupportedException;
 * capabilities() promises nothing.
 *
 * To make it the service's: override the operations it answers - author(),
 * works(), work(), search() - with
 * HttpSource's calls (getJson(), get(): every request goes
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

    public function work(Identifier|string $id): ?Work
    {
        throw NotSupportedException::operation($this->getName(), 'look up a work yet (__Name__Source::work() is not written)');
    }
}
