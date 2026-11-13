<?php

namespace __Namespace__;

use Omnibank\Config;
use Omnibank\GatewayFactory;

/**
 * "__name__": __Title__, an omnibank gateway - a skeleton: it says its name,
 * its title and its usage policy, and has no action yet, so that the
 * Gateway refuses every request (connect(), accounts(), transactions()...)
 * with omnibank's RequestNotSupportedException, and its Capabilities list
 * no operation.
 *
 *   new __Name__GatewayFactory($httpClient)   // the application's Symfony\Contracts\HttpClient\HttpClientInterface
 *
 * To make it the bank's, in populateConfig():
 *
 *   'omnibank.required_options' => ['client_id', 'client_secret'],
 *   'omnibank.api' => fn (Config $c) => new Api($this->http, ..., $c->backoff()),  // the factory keeps the client with its retries off
 *   'omnibank.action.accounts' => new AccountsAction(),   // one Action per request it answers (Request\Accounts, Balances, Transactions...)
 *
 * and "omnibank.user_key" when its limits are counted per user.
 * omnibank/qonto is a small real gateway to read. Fill policy.json in from
 * the bank's terms: Registry::guarded() holds every call to it.
 */
final class __Name__GatewayFactory extends GatewayFactory
{
    protected function populateConfig(Config $config): void
    {
        $config->defaults([
            'omnibank.factory_name' => '__name__',
            'omnibank.factory_title' => '__Title__',
            'omnibank.required_options' => [],
            // Its usage policy, as its package declares it (omnisense/contracts' schema): fill it in from the terms.
            'omnibank.policy' => __DIR__.'/policy.json',
        ]);
    }
}
