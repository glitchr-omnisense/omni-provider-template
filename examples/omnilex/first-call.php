<?php

// omnilex: legal sources. EUR-Lex (the Publications Office's SPARQL endpoint and its documents) needs no key;
// its answers are recorded in recorded/ (the consolidated text cut to the article): nothing is fetched. For a
// real call, give the factory symfony/http-client's HttpClient::create(). Légifrance and Judilibre
// (omnilex/legifrance, /judilibre) need a PISTE account.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnilex\Eurlex\EurlexSourceFactory;
use Omnilex\Registry;
use Omnisense\Contracts\Testing\RecordedHttpClient;

$http = RecordedHttpClient::routes([
    '~^GET https://publications\.europa\.eu/webapi/rdf/sparql\?.*act_consolidated_consolidates_resource_legal~' => [200, '@'.__DIR__.'/recorded/sparql-versions-32016R0679.json', ['content-type' => ['application/sparql-results+json']]],
    '~^GET https://publications\.europa\.eu/webapi/rdf/sparql\?~' => [200, '@'.__DIR__.'/recorded/sparql-work-32016R0679.json', ['content-type' => ['application/sparql-results+json']]],
    '~^GET https://publications\.europa\.eu/resource/celex/02016R0679-20160504$~' => [200, '@'.__DIR__.'/recorded/celex-02016R0679-20160504.xhtml', ['content-type' => ['application/xhtml+xml;charset=UTF-8']]],
]);
$registry = new Registry([new EurlexSourceFactory($http)], [
    'eurlex' => ['factory' => 'eurlex', 'options' => ['language' => 'fr']],
]);

// Article 17 of the GDPR as in force on 1 June 2018
$article = $registry->articles('eurlex')->article('32016R0679', '17', new DateTimeImmutable('2018-06-01'));
echo $article->title, "\n";   // Droit à l'effacement («droit à l'oubli»)
echo $article->url, "\n";     // https://eur-lex.europa.eu/legal-content/FR/TXT/?uri=CELEX:02016R0679-20160504#art_17
