# omni-provider-template

The starting point of a provider package for an omni family (omnimap, omnidrive, omnibus,
omnitrade, omnistate, omnishield, omnisearch, omniauth): the provider's classes implementing the family's
contract, the family's conformance suite wired, a `BareTest`, docs with their "Verified, and not",
the MIT licence, a changelog, and the CI and release workflows of
[glitchr-studio/ci](https://github.com/glitchr-studio/ci), pinned to a commit.

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

3. `composer install && vendor/bin/phpunit`: the generated provider passes its family's
   conformance suite as it is - a minimal, working provider. Make it the service's, keeping the
   suite green; then follow [CONTRIBUTING.md](CONTRIBUTING.md) to document and release it.

`bin/init` needs `sh` and `perl`. The families it knows are the directories under `skeleton/`
(but `common`); `.github/workflows/template.yml` generates each of them and runs its suite on every
change here.

## Licence

MIT.
