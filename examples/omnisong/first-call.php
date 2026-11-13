<?php

// omnisong: music catalogues. The iTunes Search API needs no key; its answer for one album (by its UPC) was
// recorded on 2026-11-14 (recorded/): nothing is fetched. For a real call, give the factory symfony/http-client's
// HttpClient::create() and keep to about 20 calls a minute.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnisense\Contracts\Testing\RecordedHttpClient;
use Omnisong\Catalog\Catalog;
use Omnisong\Itunes\ItunesCatalogFactory;
use Omnisong\Model\Label;
use Omnisong\Model\Reference;
use Omnisong\Registry;

$http = RecordedHttpClient::routes([
    '~^GET https://itunes\.apple\.com/lookup\?upc=028948637751&~' => [200, '@'.__DIR__.'/recorded/itunes-lookup-upc.json', ['content-type' => ['text/javascript; charset=utf-8']]],
]);
$registry = new Registry([new ItunesCatalogFactory($http)], [
    'itunes' => ['factory' => 'itunes', 'options' => ['country' => 'de']],
]);
$catalog = new Catalog($registry->all());

$release = $catalog->release(Reference::upc('028948637751'), new Label('Label Exemple', 'https://label.example.org'));
echo $release->title, "\n";                       // Beethoven: Symphonies Nos. 5 & 7
echo \count($release->tracks), " tracks\n";       // 8 tracks, each with a 30-second preview
echo $release->links->first()->url, "\n";         // https://label.example.org: the label first
