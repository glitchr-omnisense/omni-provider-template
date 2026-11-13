# __package__

__Title__ for [__core__](https://github.com/glitchr-omnisense/__core_repo__), under any PHP host.
Plain PHP: no framework in `require`.

## Install

```sh
composer require __package__
```

## First call

```php
use __Namespace__\__Name__Factory;
use Omnipulse\Model\ReachQuery;
use Omnipulse\Registry;

$registry = new Registry([new __Name__Factory()], ['main' => ['factory' => '__name__']]);
$report = $registry->audience('main')->audience(new ReachQuery(new DateTimeImmutable('-7 days'), new DateTimeImmutable()));
```

As generated, the provider answers nothing yet: the Registry refuses the audience with omnipulse's
`NotSupportedException` (its `capabilities()` promise nothing), and so does `audience()` itself.

## What to fill in

1. `__Name__Provider`: keep the contracts the service answers - `AudienceInterface` or
   `SearchPerformanceInterface` (a site's audience), `RatingInterface`, `ReviewsInterface`,
   `ReplyInterface`, `InviteInterface`, `WriteUrlInterface` (an establishment's reviews),
   `ProfileInterface`, `SponsorsInterface`, `TiersInterface` (a project's funding) - and set each
   one's `Capabilities` field.
2. `__Name__Factory`: the options the service needs (`omnipulse.required_options`), and the
   application's HTTP client taken in its constructor, kept with `Http\NoRetry::of()`, given to the
   provider with `$c->backoff()`. omnipulse/liberapay is the smallest real provider to read.
3. `policy.json`: the service's terms, read at their source (below).
4. `Tests/__Name__ProviderTest.php`: replace the "not written yet" tests with the service's answers,
   recorded (omnipulse's `Testing\RecordedHttpClient`).

## Verified, and not

- Verified (`vendor/bin/phpunit`, PHP 8.4): the provider built through omnipulse's `Registry` and
  its refusals (`Tests/__Name__ProviderTest.php`); every class loaded with the package's
  requirements alone (`BareTest`); the usage policy read and valid (`PolicyTest`); PHPStan at
  level max, its baseline empty.
- Not verified yet: anything of the service. Say here what was run against it, when, and how.

## Usage policy

`policy.json` declares what __Title__'s terms say, as data ([omnisense/contracts'
schema](https://github.com/glitchr-omnisense/contracts/blob/1.x/docs/declare-your-policy.md)): its
rate limits, its rights (commercial use, caching, storage, redistribution, the credit to show), what
every request must carry, where and when the terms were read. `composer.json` names it
(`extra.omnisense.policy`); a site that installs the package is held to it and may only make it
stricter. Until the terms are read, everything in it is unknown.

## Responsibility

__package__ is an unofficial client, affiliated with no service it reaches. The MIT licence covers
the code only and grants no right to any service or its data; whoever runs it complies with the
service's terms and obtains what they require.

## Licence

MIT.
