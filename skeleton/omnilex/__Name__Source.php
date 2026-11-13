<?php

namespace __Namespace__;

use Omnilex\Model\Capabilities;
use Omnilex\Source\HttpSource;

/**
 * __Title__, an omnilex source - a skeleton: it declares its usage policy
 * (policy.json, read by HttpSource from composer.json's
 * extra.omnisense.policy) and implements none of omnilex's capability
 * interfaces yet, so that the Registry refuses each one (search(),
 * texts(), articles()...) with omnilex's NotSupportedException, and
 * Capabilities::operations() lists nothing.
 *
 * To make it the service's: implement the interfaces of what the service
 * does, and only those - Source\SearchInterface (search(Query): Results),
 * TextReaderInterface (text($id, $at)), ArticleReaderInterface,
 * DecisionReaderInterface, CitationsInterface, RecentInterface - with
 * HttpSource's calls (getJson(), postJson(), get(): every request goes
 * through the source's usage policy, its errors mapped as the family says);
 * then say in capabilities() the kinds, identifiers and criteria it takes.
 */
final class __Name__Source extends HttpSource
{
    public const BASE_URI = 'https://api.example.org/';

    public function getName(): string
    {
        return '__name__';
    }

    public function capabilities(): Capabilities
    {
        return new Capabilities();
    }
}
