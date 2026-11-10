# Contributing a provider

How a provider of an omni family is written, tested, documented and released. The same steps for
every family; what differs is the contract and its conformance suite, named in the family core's
`docs/providers.md` (or its equivalent).

## 1. Start

```sh
gh repo create <owner>/<family>-<name> --public --template glitchr-omnisense/omni-provider-template --clone
cd <family>-<name>
bin/init <family> <name> "<Title>"
composer install
vendor/bin/phpunit
```

The suite is green before you write a line: the skeleton is a minimal provider that meets the
contract. Commit it as it is, then make it the service's.

No PHP on your machine? Run the same in the CI image:

```sh
docker run --rm -v "$PWD":/app -w /app ghcr.io/glitchr-studio/php:8.4 sh -c 'composer install && vendor/bin/phpunit'
```

To try the provider against an unreleased omnisense/contracts (a change to the kernel or its
schema), mount its checkout and let Composer take it from there, as the families' harnesses do:

```sh
docker run --rm -v "$PWD":/app -v ~/Sites/omnisense-contracts:/contracts:ro -w /app ghcr.io/glitchr-studio/php:8.4 \
    sh -c 'composer config repositories.contracts path /contracts && composer update omnisense/contracts && vendor/bin/phpunit'
```

(`composer config` writes the path repository into `composer.json`: leave it out of the commit.)

## 2. Implement

- The package is plain PHP: no framework in `require`. HTTP clients (Symfony's contracts, PSR-18)
  and caches come through the factory's constructor, never created inside. The Symfony and Laravel
  wiring lives in omnibridge, not here.
- The factory names the provider (its factory name is the configuration's `factory`), lists the
  required options and their defaults. A missing required option is the family's configuration
  exception, naming it.
- Implement the contracts the service can answer, say so in `capabilities()` / `supports()`, and
  refuse the others with the family's "not supported" exception - never a `TypeError`, never a
  silent `null`.
- Errors follow the family's rule (its core's docs): the service's refusals (401, 403, 404, 429,
  5xx) and unreadable answers (a proxy's HTML page in a 200) are the family's provider exception,
  a request that got no answer its unreachable one. Keep the service's message.
- Respect the service's terms. Its rate limits, its rights and what a request must carry are not
  code but data: `policy.json` (step 3).
- Where the family is built on [omnisense/contracts](https://github.com/glitchr-omnisense/contracts)
  (omnishield, omnistate, omnitrade in this template), the provider implements
  `DeclaresUsagePolicyInterface` (`usagePolicy()`, read from `policy.json`) or its factory names the
  file, gives it to its Capabilities, and passes the factory's `$c->backoff()` to its HTTP calls, so
  that a 429's `Retry-After` holds the next calls without sending them.

## 3. Declare its usage policy

`bin/init` writes a starting `policy.json` at the package's root, everything unknown, and names it
in `composer.json` (`extra.omnisense.policy`). Read the service's terms and usage policy at their
source, on the day you write it, and fill it in: the rate limits (a window of several units with
`every`; a quota counted in characters, units or points with `unit` and each operation's `cost`),
`concurrency`, the rights (commercial use, caching, storage, redistribution, the credit), what a
request must carry (a key, a User-Agent), the terms' address and `verifiedAt`. Unknown stays
`"unknown"`: never write "allowed" for what the terms do not say. How, field by field:
[declare your policy](https://github.com/glitchr-omnisense/contracts/blob/1.x/docs/declare-your-policy.md).
`Tests/PolicyTest.php` checks the file against the schema; assert the figures you read there. The
omnisense site's [usage policies](https://glitchr-omnisense.github.io/omnisense/apis/policies) table
shows every provider's declaration.

## 4. Test

- `Tests/<Name>ContractTest.php` uses the family's conformance trait (`<Core>\Testing\*ContractTests`).
  It runs offline, on recorded answers: give the suite the service's real answers (recorded
  once, or written from the service's documentation when no key is at hand - say which in the
  fixture's docblock). The compatibility dashboard of the omnisense site counts a package as
  running the suite when a test class holds `use <Core>\Testing\<Something>ContractTests;`.
- A check the service legitimately cannot meet becomes the trait's optional hook, documented -
  not a skipped test, not a fake pass. A check it fails is a defect: fix it.
- Your own tests beside it: what is particular to the service (its URL, its signature, its paging).
- `BareTest` proves every class loads with the package's requirements alone.
- Live checks, when the service is reachable without a key or with one from the environment, go in
  the PHPUnit group `live` (excluded by default): `vendor/bin/phpunit --group live`.
- PHPStan at the highest level: `phpstan analyse`. What `phpstan-baseline.neon` lists is accepted;
  anything new fails the CI. Keep the baseline empty in a new package.

## 5. Document

- `README.md`: what the provider is, its smallest use, and **Verified, and not**: what was run
  (the suite, offline; the live group, on which date, against what), and what was not. Never
  claim what no test ran.
- `docs/index.md`: installation, every option with its default, what it answers and what it
  refuses, its errors, the service's terms.
- `CHANGELOG.md`: every change under `## Unreleased` as you go.

## 6. Release

1. Move the `## Unreleased` entries under `## [X.Y.Z] - YYYY-MM-DD`, commit, push, and wait for the
   branch's CI to be green.
2. Tag and push the tag:

   ```sh
   git tag -a vX.Y.Z -m "X.Y.Z"
   git push origin vX.Y.Z
   ```

3. `.github/workflows/release.yml` runs the package's suite on the tag, then
   glitchr-studio/ci's `php-release.yml`: the tag is checked as a version and against the branch
   alias (the tagged commit on the branch `extra.branch-alias` names, the version in its series:
   `v1.2.0` on `1.x`), the changelog's section becomes the release's notes (no section, no
   release; a `-rc.N` without one takes `Unreleased`'s), the tag's source archive is
   attached with its SHA-256 and a build provenance attestation
   (`gh attestation verify <zip> --repo <owner>/<repo> --signer-repo glitchr-studio/ci`:
   the attestation is signed by glitchr-studio/ci's release workflow, which the package calls), and the GitHub release is published -
   a pre-release for `vX.Y.Z-rc.1` or any `0.x`.
4. Packagist: submit the repository once (https://packagist.org/packages/submit); afterwards
   Packagist's GitHub integration picks tags up, or the release workflow tells it when the
   repository holds `PACKAGIST_USERNAME` and `PACKAGIST_TOKEN` secrets.

A published tag is never moved or deleted: a mistake is a new version. The whole process, for any
package of the families: glitchr-studio/ci's
[docs/releasing.md](https://github.com/glitchr-studio/ci/blob/main/docs/releasing.md).

## 7. Make it known

Ask the family's maintainers to list the provider in the core's README and `suggest`, and the
omnisense site's catalogue (`catalog/<family>.yaml`) picks it up for the package pages and the
compatibility dashboard.
