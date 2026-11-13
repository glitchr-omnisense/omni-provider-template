<?php

namespace __Namespace__;

use Omnisense\Contracts\Policy\UsagePolicy;
use Omnisign\Config;
use Omnisign\GatewayFactory;
use Omnisign\Model\Capabilities;

/**
 * "__name__": __Title__, an omnisign gateway - a skeleton: it says its name,
 * its title and its usage policy, and has no action yet, so that the
 * Gateway refuses every request (create(), send(), fetch()...) with
 * omnisign's NotSupportedException. Its Capabilities promise no contract.
 *
 * To make it the service's, in populateConfig():
 *
 *   'omnisign.required_options' => ['api_key'],
 *   'omnisign.api' => fn (Config $c) => new Api(...),  // over the application's HTTP client (this constructor's), with $c->backoff()
 *   'omnisign.action.create' => new CreateAction(),    // one Action per request the service answers (Request\Create, Send, Fetch...)
 *
 * and in the Capabilities the levels, the contracts (signature, identity,
 * tenant file) and what its envelopes carry. omnisign/docuseal is a real
 * gateway to read. Fill policy.json in from the service's terms: the
 * gateway is guarded by it.
 */
final class __Name__GatewayFactory extends GatewayFactory
{
    private static ?UsagePolicy $policy = null;

    /** Its usage policy, as policy.json declares it (read once). */
    public static function usagePolicy(): UsagePolicy
    {
        return self::$policy ??= UsagePolicy::fromFile(__DIR__.'/policy.json');
    }

    protected function populateConfig(Config $config): void
    {
        $config->defaults([
            'omnisign.factory_name' => '__name__',
            'omnisign.factory_title' => '__Title__',
            'omnisign.required_options' => [],
            'omnisign.capabilities' => static fn (Config $c) => new Capabilities(contracts: [], policy: self::usagePolicy()),
        ]);
    }
}
