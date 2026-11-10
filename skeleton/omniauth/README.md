# __package__

__Title__ for [__core__](https://github.com/glitchr-omnisense/__core_repo__), under any PHP host.
Plain PHP: no framework in `require`.

```php
use __Namespace__\__Name__Factory;

$providers = Omniauth\OAuth\Provider\Providers::configured([
    '__name__' => ['client_id' => '...', 'client_secret' => '...', 'redirect_uri' => '/login/__name__/callback'],
], new __Name__Factory($httpClient)); // the application's Symfony\Contracts\HttpClient\HttpClientInterface
```

## Documentation

- [Installation, options, what it answers](docs/index.md)
- [Contributing](CONTRIBUTING.md): the tests, the conformance suite, a release

## Verified, and not

- Verified (`vendor/bin/phpunit`, PHP 8.4): __family__'s conformance suite
  (`Tests/__Name__ContractTest.php`, `__Vendor__\Testing\__Contract__`) on recorded answers;
  every class loaded with the package's requirements alone (`BareTest`).
- Not verified yet: the service's live answers. Say here what was run against the real service,
  when, and how (a sign-in with a test account, on which date).

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
