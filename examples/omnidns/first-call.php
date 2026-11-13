<?php

// omnidns: domains and DNS. Gandi's answer is recorded: nothing leaves the process. A real call needs a Gandi
// personal access token (or another registrar's: omnidns/cloudflare, /ovh, /scaleway).

require dirname(__DIR__).'/vendor/autoload.php';

use Omnidns\Gandi\GandiFactory;
use Omnidns\Registry;
use Omnisense\Contracts\Testing\RecordedHttpClient;

$http = RecordedHttpClient::routes([
    '~^GET https://api\.gandi\.net/v5/domain/check\?name=example\.org$~' => [200, '{"currency":"EUR","products":[{"name":"example.org","status":"available","process":"create","prices":[{"min_duration":1,"max_duration":10,"duration_unit":"y","price_after_taxes":14.4,"price_before_taxes":12}]}]}'],
]);
$registry = new Registry([new GandiFactory($http)], ['gandi' => ['factory' => 'gandi', 'options' => ['token' => 'a personal access token']]]);

$availability = $registry->availability('gandi')->check('example.org');
echo $availability->available ? 'free' : 'taken', ', ', $availability->price?->decimal(), ' ', $availability->price?->currency, "\n";   // free, 12.00 EUR
