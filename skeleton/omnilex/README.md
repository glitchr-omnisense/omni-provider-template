# __package__

__Title__ for [__core__](https://github.com/glitchr-omnisense/__core_repo__), under any PHP host.
Plain PHP: no framework in `require`.

## Install

```sh
composer require __package__
```

## First call

```php
use __Namespace__\__Name__SourceFactory;
use Omnilex\Registry;

// $http: the application's Symfony\Contracts\HttpClient\HttpClientInterface
$registry = new Registry([new __Name__SourceFactory($http)], ['main' => ['factory' => '__name__']]);
$text = $registry->texts('main')->text('LEGITEXT000006070721');
```

As generated, the source implements none of omnilex's capabilities yet: the Registry refuses each
one (`texts()`, `search()`...) with omnilex's `NotSupportedException`, before any request.

## What to fill in

1. `__Name__Source`: `BASE_URI`, then the interfaces of what the service does, and only those -
   `Source\SearchInterface`, `TextReaderInterface`, `ArticleReaderInterface`,
   `DecisionReaderInterface`, `CitationsInterface`, `RecentInterface` - written with `HttpSource`'s
   calls (`getJson()`, `postJson()`, `get()`); then the kinds, identifiers and criteria it takes in
   `capabilities()`.
2. `__Name__SourceFactory`: the options the service needs (`omnilex.required_options`) and their
   defaults. omnilex/justice-administrative is a small real source to read; a
   service behind OAuth takes the factory's token store.
3. `policy.json`: the service's terms, read at their source (below). `HttpSource` reads it from
   `composer.json`'s `extra.omnisense.policy`, and every request goes through it.
4. `Tests/__Name__SourceTest.php`: replace the "not written yet" tests with the service's answers,
   recorded.

## Verified, and not

- Verified (`vendor/bin/phpunit`, PHP 8.4): the source built through omnilex's `Registry` under its
  policy, and its refusals with nothing sent (`Tests/__Name__SourceTest.php`); every class loaded
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
