<?php

// omnischolar: scholarly works. One DOI asked to OpenAlex and to HAL; both are keyless and their answers were
// recorded on 2026-11-13 and 2026-11-14 (recorded/): nothing is fetched. For a real call, give the factories
// symfony/http-client's HttpClient::create() and your contact address (OpenAlex's polite pool).

require dirname(__DIR__).'/vendor/autoload.php';

use Omnischolar\Hal\HalSourceFactory;
use Omnischolar\OpenAlex\OpenAlexSourceFactory;
use Omnischolar\Registry;
use Omnisense\Contracts\Testing\RecordedHttpClient;

$http = RecordedHttpClient::routes([
    '~^GET https://api\.openalex\.org/works/doi:10\.1140/epjd/s10053-022-00549-6\?~' => [200, '@'.__DIR__.'/recorded/openalex-work.json'],
    '~^GET https://api\.archives-ouvertes\.fr/search/\?q=doiId_s~' => [200, '@'.__DIR__.'/recorded/hal-search.json'],
]);
$registry = new Registry([new OpenAlexSourceFactory($http), new HalSourceFactory($http)], [
    'openalex' => ['factory' => 'openalex', 'options' => ['mailto' => 'you@example.org']],
    'hal' => ['factory' => 'hal'],
]);

$work = $registry->get('openalex')->work('10.1140/epjd/s10053-022-00549-6');
echo $work->title, "\n";                                                          // Bichromatic state-dependent disordered potential...
echo $registry->get('hal')->work('10.1140/epjd/s10053-022-00549-6')->pdfUrl, "\n";   // https://iogs.hal.science/hal-03843937/document: the open copy
