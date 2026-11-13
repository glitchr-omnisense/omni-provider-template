<?php

// omnipulse: funding and audience figures. Liberapay's public profile is recorded: nothing is sent. Liberapay
// needs no key for a real call (HttpClient::create()); most other
// sources (GitHub Sponsors, Matomo, Search Console...) need a token or an account.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnipulse\Liberapay\LiberapayFactory;
use Omnipulse\Registry;
use Omnipulse\Testing\RecordedHttpClient;

// Liberapay's public profile, recorded: nothing sent. HttpClient::create() for a real call (no key needed).
$http = RecordedHttpClient::routes([
    'GET /Liberapay/public.json' => '{"username": "Liberapay", "kind": "group", "npatrons": 1782, "receiving": {"amount": "636.28", "currency": "EUR"}, "goal": {"amount": "1000.00", "currency": "EUR"}, "summaries": [{"content": "We are building the best recurrent donations platform in the world.", "lang": "en"}]}',
]);
$registry = new Registry([new LiberapayFactory($http)], ['funding' => ['factory' => 'liberapay', 'options' => ['account' => 'Liberapay']]]);

$profile = $registry->profile('funding')->profile();
echo $profile->name, ': ', $profile->sponsors, ' patrons, ', $profile->income?->monthly()?->decimal(), ' ', $profile->income?->monthly()?->currency, " a month\n";   // Liberapay: 1782 patrons, 2757.21 EUR a month
