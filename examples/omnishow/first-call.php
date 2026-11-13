<?php

// omnishow: films and series. Wikidata needs no key but asks every client for a name and a contact (the
// user_agent); its answers were recorded (recorded/): nothing is fetched. For a real call, drop $http.
// TMDB, OMDb, Trakt... (omnishow/tmdb and others) need their key.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnisense\Contracts\Testing\RecordedHttpClient;
use Omnishow\Model\Scheme;
use Omnishow\Model\WorkRef;
use Omnishow\Registry;
use Omnishow\Wikidata\WikidataFactory;

$http = RecordedHttpClient::routes([
    '~srsearch=haswbstatement%3AP345%3Dtt0079944~' => [200, '@'.__DIR__.'/recorded/search-imdb-tt0079944.json'],
    '~action=wbgetentities&ids=Q200437&props=labels%7Cdescriptions%7Cclaims%7Csitelinks~' => [200, '@'.__DIR__.'/recorded/entity-Q200437-fr.json'],
    '~action=wbgetentities&ids=Q[^&]+&props=labels&languages=fr~' => [200, '@'.__DIR__.'/recorded/labels-fr.json'],
]);
$shows = new Registry([new WikidataFactory($http)], [
    'open' => ['factory' => 'wikidata', 'options' => ['user_agent' => 'MySite/1.0 (https://example.org; films@example.org)']],
]);
$stalker = $shows->lookup('open')->work(WorkRef::imdb('tt0079944'), 'fr');
echo $stalker->title, ' (', $stalker->year, ') ', $stalker->ref(Scheme::Wikidata), "\n";   // Stalker (1979) movie:wikidata:Q200437
