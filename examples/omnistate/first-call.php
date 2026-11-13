<?php

// omnistate: public registers. The French company directory (recherche-entreprises.api.gouv.fr) and RDAP (the
// domain registries, found through IANA's bootstrap file) answered on 2026-11-13; their answers are in
// recorded/ (the company's officers left out, the bootstrap cut to .org): nothing is fetched. Both are keyless
// - for a real call, give the classes symfony/http-client's HttpClient::create() and mind the policy's rate
// limits. Most registers are keyless; each provider's policy says when one needs a key.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnisense\Contracts\Testing\RecordedHttpClient;
use Omnistate\AnnuaireEntreprises\AnnuaireEntreprises;
use Omnistate\Iana\{Bootstrap, Rdap};
use Omnistate\Omnistate;
use Omnistate\Vies\Vies;

$http = RecordedHttpClient::routes([
    '~^GET https://recherche-entreprises\.api\.gouv\.fr/search\?q=552032534&~' => [200, '@'.__DIR__.'/recorded/company-552032534.json'],
    '~^GET https://data\.iana\.org/rdap/dns\.json$~' => [200, '@'.__DIR__.'/recorded/rdap-bootstrap-dns.json'],
    '~^GET https://rdap\.publicinterestregistry\.org/rdap/domain/wikipedia\.org$~' => [200, '@'.__DIR__.'/recorded/rdap-domain-wikipedia.org.json', ['content-type' => ['application/rdap+json']]],
]);
$omnistate = new Omnistate(
    companies: [new AnnuaireEntreprises($http)],
    vat: [new Vies($http)],
    internet: new Rdap($http, new Bootstrap($http)),
);

echo $omnistate->company('552 032 534')->name, "\n";              // DANONE
echo $omnistate->domain('wikipedia.org')->registrar->name, "\n";  // MarkMonitor Inc.
