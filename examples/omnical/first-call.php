<?php

// omnical: calendars and holidays. OpenHolidays' answer for France 2027 was recorded on 2026-11-13
// (recorded/): nothing is fetched, so the run is the same every night. OpenHolidays needs no key - for a real
// call, drop $http: DeclarativeFactory::installed() then uses symfony/http-client. The data is ODbL: credit
// "OpenHolidays API" where you show it. Google and Microsoft calendars (omnical/google, /microsoft) need an account.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnical\Declarative\DeclarativeFactory;
use Omnical\Model\HolidayQuery;
use Omnical\Registry;
use Omnisense\Contracts\Testing\RecordedHttpClient;

$http = RecordedHttpClient::routes([
    '~^GET https://openholidaysapi\.org/PublicHolidays\?countryIsoCode=FR&~' => [200, '@'.__DIR__.'/recorded/public-holidays-fr-2027.json'],
]);
$calendars = new Registry(DeclarativeFactory::installed($http), [
    'holidays' => ['factory' => 'openholidays', 'options' => ['user_agent' => 'MySite/1.0 (https://example.org; contact@example.org)']],
]);
foreach ($calendars->holidays('holidays')->holidays(HolidayQuery::year('FR', 2027, null, 'fr')) as $holiday) {
    if ($holiday->nationwide) {   // the regional ones (overseas departments, Alsace-Moselle) are in the list too
        echo $holiday->first->format('Y-m-d'), ' ', $holiday->name, "\n";   // 2027-01-01 Jour de l'an ... 2027-12-25 Noël
    }
}
