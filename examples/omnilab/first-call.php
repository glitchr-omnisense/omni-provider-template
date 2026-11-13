<?php

// omnilab: public science data. PubChem needs no key (at most 5 requests a second); its answer for caffeine
// is recorded in recorded/: nothing is fetched. For a real call, drop $http. Most omnilab sources are keyless;
// the few that are not say so in their policy.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnilab\PubChem\PubChemSourceFactory;
use Omnilab\Registry;
use Omnisense\Contracts\Testing\RecordedHttpClient;

$http = RecordedHttpClient::routes([
    '~/compound/cid/2519/property/~' => [200, '@'.__DIR__.'/recorded/properties-2519.json'],
]);
$registry = new Registry([new PubChemSourceFactory($http)], [
    'pubchem' => ['factory' => 'pubchem', 'options' => ['mailto' => 'you@example.org', 'synonyms' => 0, 'description' => false]],
]);
$caffeine = $registry->get('pubchem')->entity('2519');   // caffeine, by its PubChem CID
echo $caffeine->label, ', ', $caffeine->value('molecular_formula'), ', ', $caffeine->property('molecular_weight'), "\n";
