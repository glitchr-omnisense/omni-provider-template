<?php

// omniticket: ticketing. Billetweb's answer is recorded: nothing leaves the process. A real call needs a
// Billetweb account's user id and key (HttpClient::create() in place of the recorded client).

require dirname(__DIR__).'/vendor/autoload.php';

use Omniticket\Billetweb\BilletwebFactory;
use Omniticket\Registry;
use Omniticket\Testing\RecordedHttpClient;

// Billetweb's answer, recorded: nothing leaves the process. HttpClient::create() and your key for a real call.
$http = RecordedHttpClient::routes([
    'GET /api/events' => '[{"id": "1235", "name": "Retro future shock", "start": "2036-12-31 20:00:00", "end": "2037-01-01 02:00:00", "place": "Paris", "shop": "https://www.billetweb.fr/shop.php?event=soiree", "online": "1"}]',
]);
$registry = new Registry([new BilletwebFactory($http)], ['box_office' => ['factory' => 'billetweb', 'options' => ['user' => 'recorded', 'key' => 'recorded']]]);

foreach ($registry->catalog('box_office')->events() as $event) {
    echo $event->title, ' ', $event->start?->format('d/m/Y H:i'), ' ', $event->url, "\n";   // Retro future shock 31/12/2036 20:00 https://www.billetweb.fr/...
}
