# __package__

__Title__ for [__core__](https://github.com/glitchr-omnisense/__core_repo__), under any PHP host.
Plain PHP: no framework in `require`.

## Install

```sh
composer require __package__
```

## First call

```php
use __Namespace__\__Name__GatewayFactory;
use Omnibill\Registry;

$registry = new Registry([new __Name__GatewayFactory()], ['main' => ['factory' => '__name__']]);
$inbox = $registry->get('main')->receive();
```

As generated, the gateway has no action yet: every request (`submit()`, `receive()`, `fetchStatus()`...) is
refused with omnibill's `NotSupportedException`, and its `Capabilities` promise no contract.

## What to fill in

1. `__Name__GatewayFactory::populateConfig()`: the options the service needs
   (`omnibill.required_options`), its `Api` (`omnibill.api`, over the application's HTTP client
   taken in the factory's constructor, with `$c->backoff()`), one action per request it answers
   (`omnibill.action.submit` => a `SubmitAction` of the package's `Action\`...), and in its
   `Capabilities` the syntaxes, the contracts and whether the platform is approved. omnibill/email
   is a small real gateway to read.
2. `policy.json`: the service's terms, read at their source (below); the gateway is guarded by it.
3. `Tests/__Name__GatewayTest.php`: replace the refusal test with the service's answers, recorded
   (a `MockHttpClient`'s responses).

## Verified, and not

- Verified (`vendor/bin/phpunit`, PHP 8.4): the gateway built through omnibill's `Registry` with
  its policy, and its refusal (`Tests/__Name__GatewayTest.php`); every class loaded with the
  package's requirements alone (`BareTest`); the usage policy read and valid (`PolicyTest`);
  PHPStan at level max, its baseline empty.
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
