<?php

// omnistay: holiday rentals. The local provider sells your own listings from memory: no account. Amadeus
// (omnistay/amadeus) needs an account of its own.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnistay\Local\{InMemoryStore, Listing, LocalFactory, Pricing};
use Omnistay\Model\{Guests, Location, Money, Property, PropertyKind, PropertyRef, Stay, StayQuery};
use Omnistay\Registry;

$store = new InMemoryStore([new Listing(
    new Property(new PropertyRef('homes', 'L-1'), 'Cabane des Tilleuls', PropertyKind::Cabin, new Location(45.91, 6.14, city: 'Annecy')),
    new Pricing(Money::of(9000, 'EUR'), cleaning: Money::of(4000, 'EUR')),   // 90 EUR a night, 40 EUR cleaning
)]);
$registry = new Registry([new LocalFactory($store)], ['homes' => ['factory' => 'local', 'options' => ['service_fee' => 12]]]);

$stay = Stay::of(date('Y-m-d', strtotime('+30 days')), date('Y-m-d', strtotime('+33 days')));
$results = $registry->searchAll(new StayQuery($stay, new Guests(2), latitude: 45.9, longitude: 6.13, radius: 10));
$home = $results->items[0];
echo $home->property->name, ', ', $home->nightly(), " a night, all in\n";         // Cabane des Tilleuls, 115.73 EUR a night, all in
$offer = $registry->availabilityOf($home->property->ref, $stay, new Guests(2))->cheapest();
foreach ($offer->rate->charges as $charge) {
    echo $charge->code, ' ', $charge->amount, "\n";                                // nights 270.00 EUR, cleaning 40.00 EUR, service 37.20 EUR
}
echo 'total ', $offer->total(), "\n";                                              // total 347.20 EUR
