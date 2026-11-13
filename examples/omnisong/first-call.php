<?php

// omnisong: music catalogues. The iTunes Search API needs no key; its answer for one album (by its UPC) was
// recorded on 2026-11-13 (recorded/): nothing is fetched. For a real call, give the factory symfony/http-client's
// HttpClient::create() and keep to about 20 calls a minute.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnisense\Contracts\Testing\RecordedHttpClient;
use Omnisong\Catalog\Catalog;
use Omnisong\Itunes\ItunesCatalogFactory;
use Omnisong\Model\Label;
use Omnisong\Model\Reference;
use Omnisong\Registry;

$http = RecordedHttpClient::routes([
    '~^GET https://itunes\.apple\.com/lookup\?upc=4015372820954&~' => [200, '@'.__DIR__.'/recorded/itunes-lookup-upc.json', ['content-type' => ['text/javascript; charset=utf-8']]],
]);
$registry = new Registry([new ItunesCatalogFactory($http)], [
    'itunes' => ['factory' => 'itunes', 'options' => ['country' => 'de']],
]);
$catalog = new Catalog($registry->all());

$release = $catalog->release(Reference::upc('4015372820954'), new Label('ES-DUR', 'https://www.es-dur.de'));
echo $release->title, "\n";                       // Perspectives Concertantes
echo \count($release->tracks), " tracks\n";       // 8 tracks, each with a 30-second preview
echo $release->links->first()->url, "\n";         // https://www.es-dur.de: the label first
