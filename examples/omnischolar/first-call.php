<?php

// omnischolar: scholarly works. One DOI asked to OpenAlex and to HAL; both are keyless and their answers were
// recorded on 2026-11-13 (recorded/): nothing is fetched. For a real call, give the factories
// symfony/http-client's HttpClient::create() and your contact address (OpenAlex's polite pool).

require dirname(__DIR__).'/vendor/autoload.php';

use Omnischolar\Hal\HalSourceFactory;
use Omnischolar\OpenAlex\OpenAlexSourceFactory;
use Omnischolar\Registry;
use Omnisense\Contracts\Testing\RecordedHttpClient;

$http = RecordedHttpClient::routes([
    '~^GET https://api\.openalex\.org/works/doi:10\.3762/bjoc\.10\.151\?~' => [200, '@'.__DIR__.'/recorded/openalex-work.json'],
    '~^GET https://api\.archives-ouvertes\.fr/search/\?q=doiId_s~' => [200, '@'.__DIR__.'/recorded/hal-search.json'],
]);
$registry = new Registry([new OpenAlexSourceFactory($http), new HalSourceFactory($http)], [
    'openalex' => ['factory' => 'openalex', 'options' => ['mailto' => 'you@example.org']],
    'hal' => ['factory' => 'hal'],
]);

$work = $registry->get('openalex')->work('10.3762/bjoc.10.151');
echo $work->title, "\n";                                              // Multichromophoric sugar for fluorescence photoswitching
echo $registry->get('hal')->work('10.3762/bjoc.10.151')->pdfUrl, "\n";   // https://hal.science/hal-02489392/document: the open copy
