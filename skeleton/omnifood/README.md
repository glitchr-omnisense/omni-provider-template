# __package__

__Title__ for [__core__](https://github.com/glitchr-omnisense/__core_repo__), under any PHP host.
Plain PHP: no framework in `require`.

## Install

```sh
composer require __package__
```

## First call

```php
use __Namespace__\__Name__PlatformFactory;
use Omnifood\Registry;

$registry = new Registry([new __Name__PlatformFactory()], ['main' => ['factory' => '__name__']]);
$status = $registry->guardedStore('main')->status();
```

As generated, the platform implements none of omnifood's contracts yet: the Registry refuses each
one (`guardedStore()`, `guardedOrders()`...) with omnifood's `NotSupportedException`.

## What to fill in

1. `__Name__Platform`: the contracts the platform answers, and only those - `OrdersInterface`,
   `MenuInterface`, `StoreInterface`, `ReservationsInterface`, `NotifiableInterface` (its webhooks)
   - through an `Api` over the factory's HTTP client and `Backoff`; what its orders, menus and
   reservations carry in `capabilities()`; its `Channel` (a platform none of the core's cases names
   needs its case added to omnifood first).
2. `__Name__PlatformFactory`: the options the platform needs (`omnifood.required_options`; a
   credential is best asked for when a call needs it). omnifood/zenchef is a small real platform to
   read.
3. `policy.json`: the platform's terms, read at their source (below); the Registry's guard holds
   every call to it.
4. `Tests/__Name__PlatformTest.php`: replace the "not written yet" tests with the service's answers,
   recorded.

## Verified, and not

- Verified (`vendor/bin/phpunit`, PHP 8.4): the platform built through omnifood's `Registry` under
  its policy, and its refusals (`Tests/__Name__PlatformTest.php`); every class loaded
  with the package's requirements alone (`BareTest`); the usage policy read and valid
  (`PolicyTest`); PHPStan at level max, its baseline empty.
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
