# __package__

__Title__ for [__core__](https://github.com/glitchr-studio/__core_repo__), under any PHP host.
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

## Responsibility

__package__ is an unofficial client, affiliated with no service it reaches. The MIT licence covers
the code only and grants no right to any service or its data; whoever runs it complies with the
service's terms and obtains what they require.

## Licence

MIT.
