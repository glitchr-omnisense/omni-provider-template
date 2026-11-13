<?php

// omnimap: maps. OpenStreetMap's tiles need no key: the server side describes the map, assets/omnimap.js draws
// it in the page. omnimap/google needs a Google Maps key; omnimap/maplibre
// draws vector tiles from a style.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnimap\MapHtml;
use Omnimap\Model\{LatLng, MapView, Marker};
use Omnimap\OpenStreetMap\OpenStreetMapFactory;
use Omnimap\Registry;

$registry = new Registry([new OpenStreetMapFactory()], [
    'maps' => ['factory' => 'openstreetmap', 'options' => ['user_agent' => 'MySite/1.0 (+https://example.org)']],
]);
$view = new MapView(new LatLng(35.0116, 135.7681), 12, [new Marker(new LatLng(35.0036, 135.7780), 'Gion')], id: 'map');
$description = $registry->renderer('maps')->describe($view);
$html = (new MapHtml())->live('map', $description);

echo $description['provider'], ', ', \count($description['view']['markers']), ' marker: ', $description['view']['markers'][0]['title'], "\n";   // openstreetmap, 1 marker: Gion
echo strstr($html, ' data-omnimap-description', true), " ...>\n";   // <div id="map" data-omnimap="map" ...>
