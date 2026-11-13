<?php

// omnitravel: public transport timetables. A tiny GTFS feed written on the spot and imported into SQLite in
// memory: no network, no key. Real feeds are downloaded from the operator; real-time feeds need the HTTP client.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnitravel\Gtfs\{Feed, GtfsFactory, Importer};
use Omnitravel\Model\DepartureQuery;
use Omnitravel\Registry;

$dir = sys_get_temp_dir().'/omnitravel-example';
@mkdir($dir);
file_put_contents("$dir/agency.txt", "agency_id,agency_name,agency_url,agency_timezone\nX,Example Transit,https://transit.example.org,Europe/Paris\n");
file_put_contents("$dir/stops.txt", "stop_id,stop_name,stop_lat,stop_lon\nGARE,Gare,47.4645,-0.5567\nRALL,Ralliement,47.4712,-0.5518\n");
file_put_contents("$dir/routes.txt", "route_id,agency_id,route_short_name,route_type\nA,X,A,0\n");
file_put_contents("$dir/trips.txt", "route_id,service_id,trip_id,trip_headsign\nA,DAILY,A-1,Ralliement\n");
file_put_contents("$dir/stop_times.txt", "trip_id,arrival_time,departure_time,stop_id,stop_sequence\nA-1,08:00:00,08:00:00,GARE,1\nA-1,08:06:00,08:06:00,RALL,2\n");
file_put_contents("$dir/calendar.txt", "service_id,monday,tuesday,wednesday,thursday,friday,saturday,sunday,start_date,end_date\nDAILY,1,1,1,1,1,1,1,20260101,20361231\n");

$gtfs = new GtfsFactory();                                   // the HTTP client reads real-time feeds only: none here
(new Importer($gtfs->store('sqlite::memory:')))->import(new Feed($dir), 'example');
$travel = new Registry([$gtfs], ['example' => ['factory' => 'gtfs', 'options' => ['feed' => 'example', 'dsn' => 'sqlite::memory:']]]);

$from = new DateTimeImmutable('2030-06-03 07:30', new DateTimeZone('Europe/Paris'));
foreach ($travel->timetables('example')->departures(new DepartureQuery('GARE', $from, limit: 2)) as $departure) {
    echo $departure->time()->format('D Y-m-d H:i'), ' line ', $departure->line?->name, ' to ', $departure->headsign, "\n";   // Mon 2030-06-03 08:00 line A to Ralliement, then Tuesday's
}
