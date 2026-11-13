<?php

// omnibus: shipping. The offline carrier needs no account: the shop's own rates and a tracking number typed by hand.
// A carrier's package (omnibus/colissimo, omnibus/ups...) and its account add real labels and tracking.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnibus\Model\{Address, Parcel, Shipment};
use Omnibus\Offline\OfflineGatewayFactory;
use Omnibus\Registry;

$registry = new Registry([new OfflineGatewayFactory()], [
    'shop' => ['factory' => 'offline', 'options' => ['rates' => [['service' => 'courier', 'label' => 'Courier', 'bands' => [1000 => 690, 30000 => 1290]]]]],
]);
$gateway = $registry->get('shop');

$shipment = new Shipment(
    new Address('The shop', ['1 rue de Rivoli'], '75001', 'Paris', 'FR'),
    new Address('Camille Durand', ['12 rue des Juifs'], '67000', 'Strasbourg', 'FR'),
    [new Parcel(500)],
    options: ['tracking_number' => '6A12345678901'],
);
echo $gateway->rate($shipment)[0]->amount, "\n";          // 690: cents, for 500 g
echo $gateway->ship($shipment)->trackingNumber, "\n";     // 6A12345678901
