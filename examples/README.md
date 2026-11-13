# First calls

One first call per omnisense family, in plain PHP (no framework), installed from Packagist the way a newcomer
installs it: no lock file, no path repository, Composer's default `minimum-stability` (stable). The
[examples workflow](../.github/workflows/examples.yml) installs and runs all of them on every push and every night.

No example needs an account, a key, or the network beyond Packagist. Where a family's real provider needs
credentials, the example runs its offline provider, or gives the provider an HTTP client that answers from
recorded responses (`Omnisense\Contracts\Testing\RecordedHttpClient`, or the family's own `Testing` client).
Where the service is public and keyless, its answers were recorded once and are kept in the family's `recorded/`
folder, so every run prints the same thing. Each script's opening comment says what a real call needs instead.

## Run them

```sh
git clone https://github.com/glitchr-omnisense/omni-provider-template.git && cd omni-provider-template/examples
composer install
php omnibus/first-call.php
bin/run-examples          # every example, compared with its expected.txt
```

Without PHP on the machine (PHP 8.4 or later, with Composer):

```sh
docker run --rm -v "$PWD:/app" -w /app composer:2 sh -c 'composer install && sh bin/run-examples'
```

To start a project of your own from one of them, require the same packages in an empty folder, for example
`composer require omnisense/omnibus omnibus/offline`, and copy the script (its autoload path becomes
`__DIR__.'/vendor/autoload.php'`).

## The families

| Family | Packages | Script | Prints |
|---|---|---|---|
| omniauth: sign-in | `omnisense/omniauth` | [omniauth/first-call.php](omniauth/first-call.php) | `authenticated as ada` |
| omnibank: bank access | `omnisense/omnibank` `omnibank/files` | [omnibank/first-call.php](omnibank/first-call.php) | the two lines of a CSV statement |
| omnibill: e-invoicing | `omnisense/omnibill` `omnibill/email` | [omnibill/first-call.php](omnibill/first-call.php) | `F-2026-042: Envoyée` |
| omnibus: shipping | `omnisense/omnibus` `omnibus/offline` | [omnibus/first-call.php](omnibus/first-call.php) | a rate (`690` cents), a tracking number |
| omnical: holidays | `omnisense/omnical` `omnical/openholidays` | [omnical/first-call.php](omnical/first-call.php) | France's public holidays of 2027 (recorded) |
| omnicrawl: polite page reading | `omnisense/omnicrawl` | [omnicrawl/first-call.php](omnicrawl/first-call.php) | `An article` (robots.txt read first) |
| omnidns: domains | `omnisense/omnidns` `omnidns/gandi` | [omnidns/first-call.php](omnidns/first-call.php) | `free, 12.00 EUR` (recorded) |
| omnidrive: file storage | `omnisense/omnidrive` `omnidrive/local` | [omnidrive/first-call.php](omnidrive/first-call.php) | `Hello.` and its public URL |
| omnifood: delivery platforms | `omnisense/omnifood` `omnifood/ubereats` | [omnifood/first-call.php](omnifood/first-call.php) | `#A1B2C new: 2 x Shoyu ramen` (recorded) |
| omnilab: public science data | `omnisense/omnilab` `omnilab/pubchem` | [omnilab/first-call.php](omnilab/first-call.php) | `Caffeine, C8H10N4O2, 194.19 g/mol` (recorded) |
| omnilang: translation | `omnisense/omnilang` `omnilang/deepl` | [omnilang/first-call.php](omnilang/first-call.php) | `Double room with a view` (recorded) |
| omnilex: legal sources | `omnisense/omnilex` `omnilex/eurlex` | [omnilex/first-call.php](omnilex/first-call.php) | GDPR article 17 as in force on 2018-06-01 (recorded) |
| omnimap: maps | `omnisense/omnimap` `omnimap/openstreetmap` | [omnimap/first-call.php](omnimap/first-call.php) | the map's description and its `<div>` |
| omnimeet: video meetings | `omnisense/omnimeet` `omnimeet/direct` | [omnimeet/first-call.php](omnimeet/first-call.php) | the room a participant joins |
| omniplay: game services | `omnisense/omniplay` | [omniplay/first-call.php](omniplay/first-call.php) | a ticket's claims, then `NULL` on another server |
| omnipost: sharing | `omnisense/omnipost` | [omnipost/first-call.php](omnipost/first-call.php) | a share link |
| omnipulse: funding figures | `omnisense/omnipulse` `omnipulse/liberapay` | [omnipulse/first-call.php](omnipulse/first-call.php) | `Liberapay: 1782 patrons, ...` (recorded) |
| omnischolar: scholarly works | `omnisense/omnischolar` `omnischolar/openalex` `omnischolar/hal` | [omnischolar/first-call.php](omnischolar/first-call.php) | a work's title and its open copy (recorded) |
| omnisearch: full-text search | `omnisense/omnisearch` | [omnisearch/first-call.php](omnisearch/first-call.php) | two notes found, by rank |
| omnishield: spam and abuse | `omnisense/omnishield` `omnishield/altcha` `omnishield/disposable` | [omnishield/first-call.php](omnishield/first-call.php) | `altcha-widget`, `bool(true)` |
| omnishow: films and series | `omnisense/omnishow` `omnishow/wikidata` | [omnishow/first-call.php](omnishow/first-call.php) | `Stalker (1979) movie:wikidata:Q200437` (recorded) |
| omnisign: e-signature | `omnisense/omnisign` `omnisign/docuseal` | [omnisign/first-call.php](omnisign/first-call.php) | `4 sent` and the signing URL (recorded) |
| omnisong: music catalogues | `omnisense/omnisong` `omnisong/itunes` | [omnisong/first-call.php](omnisong/first-call.php) | an album found by its UPC (recorded) |
| omnistate: public registers | `omnisense/omnistate` `omnistate/annuaire-entreprises` `omnistate/iana` `omnistate/vies` | [omnistate/first-call.php](omnistate/first-call.php) | `DANONE`, `MarkMonitor Inc.` (recorded) |
| omnistay: holiday rentals | `omnisense/omnistay` `omnistay/local` | [omnistay/first-call.php](omnistay/first-call.php) | a stay's price, charge by charge |
| omniticket: ticketing | `omnisense/omniticket` `omniticket/billetweb` | [omniticket/first-call.php](omniticket/first-call.php) | an event (recorded) |
| omnitrade: payments | `omnisense/omnitrade` `omnitrade/paypal` | [omnitrade/first-call.php](omnitrade/first-call.php) | `pending` and PayPal's approval page (recorded) |
| omnitravel: timetables | `omnisense/omnitravel` `omnitravel/gtfs` | [omnitravel/first-call.php](omnitravel/first-call.php) | the next departures of a tiny GTFS feed |

Every package of a family is listed on [the omnisense site](https://glitchr-omnisense.github.io/omnisense/), with
its usage policy: what needs a key, the rate limits, the attribution a provider asks for.

## How the check works

`bin/run-examples` runs every `<family>/first-call.php` and compares what it prints with `<family>/expected.txt`.
A family whose provider has no stable release yet can carry a `PENDING` file (one line: why); it is then skipped
and reported, not failed. The workflow runs the check in two images, the official `composer:2` (the latest PHP)
and the families' CI image (PHP 8.4).

## Licence

MIT. The recorded answers are the services' own data, kept for the examples: OpenHolidays (ODbL), the French
company directory, IANA and the .org registry, EUR-Lex, OpenAlex (CC0), HAL, the iTunes Search API, Wikidata
(CC0), PubChem.
