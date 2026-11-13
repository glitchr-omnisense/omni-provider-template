<?php

namespace __Namespace__;

use Omnisense\Contracts\Policy\UsagePolicy;
use Omnibill\Config;
use Omnibill\GatewayFactory;
use Omnibill\Model\Capabilities;

/**
 * "__name__": __Title__, an omnibill gateway - a skeleton: it says its name,
 * its title and its usage policy, and has no action yet, so that the
 * Gateway refuses every request (submit(), receive(), fetchStatus()...) with
 * omnibill's NotSupportedException. Its Capabilities promise no contract.
 *
 * To make it the service's, in populateConfig():
 *
 *   'omnibill.required_options' => ['api_key'],
 *   'omnibill.api' => fn (Config $c) => new Api(...),  // over the application's HTTP client (this constructor's), with $c->backoff()
 *   'omnibill.action.submit' => new SubmitAction(),    // one Action per request the service answers (Request\Submit, Receive, FetchStatus...)
 *
 * and in the Capabilities the syntaxes, the contracts and whether the
 * platform is approved. omnibill/email is a small real gateway to read.
 * Fill policy.json in from the service's terms: the gateway is guarded by it.
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
            'omnibill.factory_name' => '__name__',
            'omnibill.factory_title' => '__Title__',
            'omnibill.required_options' => [],
            'omnibill.capabilities' => static fn (Config $c) => new Capabilities(syntaxes: [], contracts: [], policy: self::usagePolicy()),
        ]);
    }
}
