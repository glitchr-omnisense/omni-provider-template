# omni-provider-template

The starting point of a provider package for an omni family (omnimap, omnidrive, omnibus,
omnitrade, omnistate, omnishield, omnisearch, omniauth), on
[omnisense/contracts](https://github.com/glitchr-omnisense/contracts) 1.0.0-rc.1 or later:

- the provider's classes implementing the family's contract, its conformance suite wired, and
  answers read through `Data` (the kernel's `Http\Data`), so that PHPStan runs at its highest
  level with an empty baseline from the first commit;
- a starting usage policy as data (`policy.json`, everything unknown until the terms are read,
  named in `extra.omnisense.policy`) and its test;
- the HTTP client kept from replaying a write (`Http\NoRetry`, with a test over Symfony's
  `RetryableHttpClient`) and the service's waits honoured (`$c->backoff()`);
- in the omnibus skeleton, the three kinds of request a registry's guard does not see, each
  counted through the factory's guard and tested: an OAuth token request (`token`), the further
  pages of a listing (`PolicyGuard::pages()`), a request limited per resource (`trackParcel`, per
  parcel);
- a `BareTest` (the kernel's bare check), docs with their "Verified, and not", the MIT licence, a
  changelog, a `.gitattributes` keeping tests and CI files out of the archive, and the CI and
  release workflows of [glitchr-studio/ci](https://github.com/glitchr-studio/ci), pinned to a
  commit.

The other families have a starting skeleton (omnical, omnicrawl, omnidns, omnifood, omnilab, omnilang, omnilex, omniplay, omnipost, omnipulse, omnischolar, omnishow, omnisong, omnistay, omniticket, omnitravel), on their 1.0 cores from Packagist
at the default minimum-stability: the provider class implementing the family's provider contract, its
methods refused with the family's `NotSupportedException` ("not written yet") and its capabilities
promising nothing, the starting `policy.json` and its test, a first-call test through the family's
registry, the `BareTest`, and a README saying what to fill in. No conformance suite is wired: it
would fail on purpose until the provider answers.

[CONTRIBUTING.md](CONTRIBUTING.md) is the way through, and ends with the checklist a maintainer
or an API company follows.

## Use it

1. On GitHub, **Use this template** → a repository named `<family>-<name>` (for example
   `omnimap-maplibre`), or from a terminal:

   ```sh
   gh repo create <owner>/omnimap-maplibre --public --template glitchr-omnisense/omni-provider-template --clone
   cd omnimap-maplibre
   ```

2. Fill the names:

   ```sh
   bin/init omnimap maplibre "MapLibre"
   ```

   The skeleton of the family is copied to the root with every name filled (`omnimap/maplibre`,
   `Omnimap\MapLibre\MapLibreFactory`, the factory name `maplibre`...), then `skeleton/` and
   `bin/init` are removed. Nothing is committed.

3. `composer install && vendor/bin/phpunit && vendor/bin/phpstan analyse`: the generated
   provider passes its family's conformance suite and PHPStan as it is - a minimal, working
   provider. Make it the service's, keeping the
   suite green; then follow [CONTRIBUTING.md](CONTRIBUTING.md) to document and release it.

`bin/init` needs `sh` and `perl`. The families it knows are the directories under `skeleton/`
(but `common` and `parts`, the pieces several families share); `.github/workflows/template.yml`
generates each of them and runs its suite and PHPStan on every change here.

## Licence

MIT.
