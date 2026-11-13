<?php

// omnifood: delivery platforms. Uber's two answers (a token, then the order) are recorded: no account, nothing
// leaves the process. A real call needs an Uber Eats (or Deliveroo, Just Eat...) partner account.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnifood\Registry;
use Omnifood\UberEats\UberEatsPlatformFactory;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

$http = new MockHttpClient(static fn (string $method, string $url) => new MockResponse(str_contains($url, '/oauth/')
    ? '{"access_token": "recorded", "expires_in": 3600}'
    : '{"order": {"id": "o-1", "display_id": "A1B2C", "state": "OFFERED", "fulfillment_type": "DELIVERY_BY_UBER",
        "carts": [{"items": [{"id": "ramen", "title": "Shoyu ramen", "quantity": {"amount": 2}}]}]}}'));
$registry = new Registry([new UberEatsPlatformFactory($http)], [
    'ubereats' => ['factory' => 'ubereats', 'options' => ['client_id' => 'id', 'client_secret' => 'secret', 'store_id' => 'store-1']],
]);

$order = $registry->get('ubereats')->order('o-1');
echo '#', $order->displayId, ' ', $order->status->value, ': ', $order->lines[0]->quantity, ' x ', $order->lines[0]->name, "\n";
// #A1B2C new: 2 x Shoyu ramen
