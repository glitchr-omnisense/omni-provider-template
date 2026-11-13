<?php

namespace __Namespace__;

use Omnimeet\Config;
use Omnimeet\GatewayFactory;
use Omnimeet\Model\Capabilities;

/**
 * "__name__": __Title__, an omnimeet gateway - a skeleton: it says its name,
 * its title and its usage policy, and has no action yet, so that the
 * Gateway refuses every request (open(), join(), close(), fetch()...) with
 * omnimeet's NotSupportedException, and its Capabilities list no operation.
 *
 * To make it the service's, in populateConfig():
 *
 *   'omnimeet.required_options' => ['api_key'],
 *   'omnimeet.api' => fn (Config $c) => new Api(...),  // over the application's HTTP client (this constructor's), with $c->backoff()
 *   'omnimeet.action.open' => new OpenAction(),        // one Action per request it answers (Request\Open, Join, Close...)
 *
 * and in the Capabilities what its meetings are (participants, end-to-end,
 * recording, the access it gives, its operations). omnimeet/jitsi is a real
 * gateway to read. Fill policy.json in from the service's terms:
 * Registry::guarded() holds every call to it.
 */
final class __Name__GatewayFactory extends GatewayFactory
{
    protected function populateConfig(Config $config): void
    {
        $config->defaults([
            'omnimeet.factory_name' => '__name__',
            'omnimeet.factory_title' => '__Title__',
            'omnimeet.required_options' => [],
            // Its usage policy, as its package declares it (omnisense/contracts' schema): fill it in from the terms.
            'omnimeet.policy' => __DIR__.'/policy.json',
            'omnimeet.capabilities' => new Capabilities(),
        ]);
    }
}
