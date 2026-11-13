<?php

// omnishield: spam and abuse. ALTCHA (a proof of work the browser solves) and the disposable-address list run
// on this machine, no account. omnishield/recaptcha, /turnstile, /akismet need the service's keys.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnishield\Altcha\AltchaGatewayFactory;
use Omnishield\Disposable\DisposableGatewayFactory;
use Omnishield\Model\Identity;
use Omnishield\Registry;
use Omnishield\Replay\InMemoryReplayStore;

$registry = new Registry([new AltchaGatewayFactory(new InMemoryReplayStore()), new DisposableGatewayFactory()], [
    'forms' => ['factory' => 'altcha', 'options' => ['hmac_key' => 'a-long-random-secret-of-the-site']],
    'emails' => ['factory' => 'disposable'],
]);

echo $registry->challenge('forms')->widget('contact')->tag, "\n";                                       // altcha-widget
var_dump($registry->reputation('emails')->lookup(new Identity(email: 'someone@mailinator.com'))->known);  // bool(true)
