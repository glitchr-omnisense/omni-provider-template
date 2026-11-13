# __package__

__Title__ for [__core__](https://github.com/glitchr-omnisense/__core_repo__), under any PHP host.
Plain PHP: no framework in `require`.

## Install

```sh
composer require __package__
```

## First call

```php
use __Namespace__\__Name__ProviderFactory;
use Omnipost\Registry;

$registry = new Registry([new __Name__ProviderFactory()], ['main' => ['factory' => '__name__']]);
$feed = $registry->feed('main')->feed();
```

As generated, the provider answers nothing yet: `account()` and `feed()` are refused with omnipost's
`NotSupportedException`, and the Registry refuses `publisher()` (it does not publish yet).

## What to fill in

1. `__Name__Provider`: `account()` and `feed()`, and `PublisherInterface` if the network publishes
   (`publish()`, `status()`, `delete()`, the `capabilities()` its posts must meet), through an `Api`
   over the factory's HTTP client and `Backoff`. `getPlatform()` is the `Platform` case named like
   the factory: a network none of them names needs its case added to omnipost first.
2. `__Name__ProviderFactory`: the options the network needs (`omnipost.required_options`).
   omnipost/instagram is a real provider to read.
3. `policy.json`: the network's terms, read at their source (below); the Registry's guard holds
   every call to it.
4. `Tests/__Name__ProviderTest.php`: replace the "not written yet" tests with the service's answers,
   recorded.

## Verified, and not

- Verified (`vendor/bin/phpunit`, PHP 8.4): the provider built through omnipost's `Registry` under
  its policy, and its refusals (`Tests/__Name__ProviderTest.php`); every class loaded
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
