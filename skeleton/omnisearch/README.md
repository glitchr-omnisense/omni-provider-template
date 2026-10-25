# __package__

__Title__ for [__core__](https://github.com/glitchr-studio/__core_repo__), under any PHP host.
Plain PHP: no framework in `require`.

```php
use __Namespace__\__Name__Factory;

$driver = (new __Name__Factory())->create([/* options: docs/index.md */]);
```

With the family's `Registry`, by its engine name `__name__`:

```php
$registry = new __Vendor__\Registry([new __Name__Factory()], ['default' => ['driver' => '__name__', 'options' => []]]);
```

## Documentation

- [Installation, options, what it answers](docs/index.md)
- [Contributing](CONTRIBUTING.md): the tests, the conformance suite, a release

## Verified, and not

- Verified (`vendor/bin/phpunit`, PHP 8.4): __family__'s conformance suite
  (`Tests/__Name__ContractTest.php`, `__Vendor__\Testing\__Contract__`);
  every class loaded with the package's requirements alone (`BareTest`).
- Not verified yet: the engine itself. Say here what was run against a real server, when, and
  how (a `live` PHPUnit group, a server in Docker).

## Responsibility

__package__ is an unofficial client, affiliated with no engine it reaches. The MIT licence covers
the code only; whoever runs it complies with the engine's terms.

## Licence

MIT.
