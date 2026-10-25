# __package__

__Title__ for [__core__](https://github.com/glitchr-studio/__core_repo__), under any PHP host.
Plain PHP: no framework in `require`.

```php
use __Namespace__\__Name__Factory;

$provider = (new __Name__Factory())->create([/* options: docs/index.md */]);
```

With the family's `Registry`, by its factory name `__name__`:

```php
$registry = new __Vendor__\Registry([new __Name__Factory()], ['main' => ['factory' => '__name__', 'options' => []]]);
```

## Documentation

- [Installation, options, what it answers](docs/index.md)
- [Contributing](CONTRIBUTING.md): the tests, the conformance suite, a release

## Verified, and not

- Verified (`vendor/bin/phpunit`, PHP 8.4): __family__'s conformance suite
  (`Tests/__Name__ContractTest.php`, `__Vendor__\Testing\__Contract__`) on recorded answers;
  every class loaded with the package's requirements alone (`BareTest`).
- Not verified yet: the service's live answers. Say here what was run against the real service,
  when, and how (a `live` PHPUnit group, keyless or with keys from the environment).

## Responsibility

__package__ is an unofficial client, affiliated with no service it reaches. The MIT licence covers
the code only and grants no right to any service or its data; whoever runs it complies with the
service's terms and obtains what they require.

## Licence

MIT.
