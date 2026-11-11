# Contributing a provider

How a provider of an omni family is written, tested, documented and released. The same steps for
every family; what differs is the contract and its conformance suite, named in the family core's
`docs/providers.md` (or its equivalent). Whether you maintain the package or work for the company
whose API it calls, the [checklist](#checklist) at the end is what a pull request is read against.

## 1. Start

```sh
gh repo create <owner>/<family>-<name> --public --template glitchr-omnisense/omni-provider-template --clone
cd <family>-<name>
bin/init <family> <name> "<Title>"
composer install
vendor/bin/phpunit
vendor/bin/phpstan analyse
```

The suite is green and PHPStan finds nothing before you write a line: the skeleton is a minimal
provider that meets the contract, declares an unknown usage policy, sends no write twice and loads
with its requirements alone. Commit it as it is, then make it the service's.

No PHP on your machine? Run the same in the CI image:

```sh
docker run --rm -v "$PWD":/app -w /app ghcr.io/glitchr-studio/php:8.4 \
    sh -c 'composer install && vendor/bin/phpunit && vendor/bin/phpstan analyse'
```

To try the provider against an unreleased omnisense/contracts (a change to the kernel or its
schema), or an unreleased family core, mount the checkout and let Composer take it from there, as
the families' harnesses do:

```sh
docker run --rm -v "$PWD":/app -v ~/Sites/omnisense-contracts:/contracts:ro -w /app ghcr.io/glitchr-studio/php:8.4 \
    sh -c 'composer config repositories.contracts path /contracts && composer update omnisense/contracts && vendor/bin/phpunit'
```

(the same with `-v <core checkout>:/core:ro` and `repositories.core path /core` for the core.
`composer config` writes the path repository into `composer.json`: leave it out of the commit.)
The skeleton needs omnisense/contracts 1.0.0-rc.1 or later (`Http\NoRetry`, `PolicyGuard::pages()`,
limits per resource); `^1.0@dev` with `prefer-stable` installs the latest tag.

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
  a request that got no answer its unreachable one. Keep the service's message, never a secret.
- **Read answers through `Data`** (generated beside your classes, over omnisense/contracts'
  `Http\Data`): `Data::of(json_decode($body, true))`, then `string('id')`, `int('amount')`,
  `list('events')`, `dateTime('at')`, each giving its type or null whatever the service sent. Never
  `(string) $data['id']` on a `mixed`: PHPStan at level max refuses it, and an answer of another
  shape would pass unseen.
- **Writes are sent once.** The application's client may retry (Symfony's `retry_failed` resends a
  POST answered 503): the client is wrapped in `Http\NoRetry::of()` where it comes in - your
  factory's constructor or your provider's, or the family's factory where its core does it
  (omnibus, omnitrade). The family's code, never the client, decides what to send again. omnidrive
  and omnisearch call their engines through libraries and take no HTTP client here.
- **Honour the service's waits.** Pass the factory's `$c->backoff()` to the HTTP calls (the
  family's `JsonClient`, `Http\Answer` or `Http\Throttle`): a 429 and its `Retry-After` (or a
  `RateLimit` field) hold the next calls without sending them.
- **Count what the registry does not see.** The registry's guard counts each family operation
  once. A request the provider makes besides goes through a guard its factory gives,
  `$c->guard(UsagePolicy::fromFile(POLICY))`, which counts in the same limiter:
  - an OAuth token request: `$guard->call('token', fn () => ...)` (the omnibus skeleton's `Api`);
  - each page of a listing after the first: `$guard->pages($operation, $first, $next)`, or the
    loop with `call()` that omnisense/contracts' docs/policies.md shows ("Requests inside an
    operation"; the omnibus skeleton's `Action\PickupPointsAction`);
  - a request about one resource the terms limit per resource (6 trackings a day per parcel):
    `$guard->perResource()->call('trackParcel', fn () => ..., ['resource' => $trackingNumber])` -
    the resource always named, or the guard throws `ResourceNotNamed` (the omnibus skeleton's
    `Action\TrackingAction`).
  The families without a provider-held guard (omnistate's registers, omnimap's tiles, omniauth's
  sign-ins) are guarded by their core; their docs say what is counted.

## 3. Declare its usage policy

`bin/init` writes a starting `policy.json` at the package's root, everything unknown, and names it
in `composer.json` (`extra.omnisense.policy`). Fill it in from the service's own pages, as
[declare your policy](https://github.com/glitchr-omnisense/contracts/blob/1.x/docs/declare-your-policy.md)
says field by field:

1. **Read the provider's terms and usage policy today, at their source** - not a blog post, a
   search snippet, another package's file or memory. A page you cannot read (a 403, a login wall,
   a page rendered by script) is not read: say so in `provenance.notes`.
2. **Record where and when**: `provenance.terms` and `provenance.policy` are the pages you read,
   `provenance.verifiedAt` the day (`YYYY-MM-DD`); each plan's figures carry their own `source`
   and `verifiedAt`. Read again later? Update the date.
3. **Mark as unknown what isn't published**: `"rateLimits": null`, `"unknown"` for a permission,
   `null` for `openSourceRequired` or a retention. Never write "allowed" for what the terms do not
   say: a site can tighten a declaration, never loosen it, so a wrong "allowed" can't be undone by
   the sites that trust it. A figure seen elsewhere goes in `notes`, not in a limit.

Every field at once, on an imaginary service (valid against the schema):

```json
{
    "$schema": "https://github.com/glitchr-omnisense/contracts/blob/1.x/resources/usage-policy.schema.json",
    "provider": "example",
    "rateLimits": [
        {"max": 100, "window": "second", "scope": "key", "note": "Per API key, every endpoint."},
        {"max": 500, "window": "minute", "every": 5, "operations": ["search"]},
        {"max": 10000, "window": "day", "unit": "units", "cost": {"search": 100, "fetch": 1}},
        {"max": 6, "window": "day", "scope": "resource", "per": "parcel", "operations": ["trackParcel"]},
        {"max": 10, "window": "hour", "operations": ["token"]}
    ],
    "concurrency": 4,
    "plans": {"default": "free", "plans": {
        "free": {
            "rateLimits": [
                {"max": 100, "window": "second", "scope": "key", "note": "Per API key, every endpoint."},
                {"max": 500, "window": "minute", "every": 5, "operations": ["search"]},
                {"max": 10000, "window": "day", "unit": "units", "cost": {"search": 100, "fetch": 1}},
                {"max": 6, "window": "day", "scope": "resource", "per": "parcel", "operations": ["trackParcel"]},
                {"max": 10, "window": "hour", "operations": ["token"]}
            ],
            "concurrency": 4, "source": "https://example.org/pricing", "verifiedAt": "2026-11-11"
        },
        "pro": {
            "rateLimits": [
                {"max": 1000, "window": "second", "scope": "key"},
                {"max": 1000000, "window": "day", "unit": "units", "cost": {"search": 100, "fetch": 1}},
                {"max": 6, "window": "day", "scope": "resource", "per": "parcel", "operations": ["trackParcel"]}
            ],
            "concurrency": 16, "source": "https://example.org/pricing", "verifiedAt": "2026-11-11"
        }
    }},
    "rights": {
        "commercial": "allowed",
        "openSourceRequired": false,
        "caching": {"permission": "allowed", "maxTtl": 86400},
        "storage": "unknown",
        "redistribution": "forbidden",
        "attribution": {"required": true, "text": "Data by Example", "url": "https://example.org", "where": ["display"]},
        "licence": null,
        "retention": {"maxAge": 2592000, "covers": "Any data the API returns, 30 days after it was fetched."}
    },
    "requirements": {"key": true, "userAgent": {"required": true, "pattern": "~^[^/ ]+/[0-9]~"}},
    "provenance": {
        "terms": "https://example.org/terms",
        "policy": "https://example.org/docs/rate-limits",
        "verifiedAt": "2026-11-11",
        "declaredBy": "maintainer",
        "contact": "api@example.org",
        "notes": "Read 2026-11-11 at the two pages above. Storage: the terms do not say."
    }
}
```

- `window` is `second`, `minute`, `hour`, `day` or `month`; `every` makes it several of them (500
  every 5 minutes is not 100 a minute, which would be stricter than the terms).
- `scope`: `application` (the default), `key`, `client-ip`, `user`, or `resource` with `per` (the
  kind of resource, lower case): such a limit names its `operations`, and the provider passes the
  resource's key when it calls (step 2).
- `operations` are the family's operation names (its contract methods: `track`, `pickupPoints`,
  `purchase`...) or the provider's own for the requests it counts itself (`token`, `trackParcel`).
  No `operations`: every operation counts.
- `unit` (`requests` by default, `characters`, `units`, `points`, `content-bytes`) and `cost` (per
  operation, 1 by default) for a quota counted in something else than calls.
- `concurrency`: the requests the terms let run at once.
- `plans`: when the figures depend on the subscription. `default` is the free or most restrictive
  plan and the top-level `rateLimits` and `concurrency` are its own; the factory's `plan` option
  picks another. A plan that publishes no figure is not declared.
- `rights.retention`: how long the data may be kept at all (`maxAge` in seconds, `covers` in the
  terms' words), unlike `caching.maxTtl` (how long an answer may be served again); the guard's
  `cacheTtl()` is never longer than it.

`Tests/PolicyTest.php` checks the file against the schema; assert the figures you read there. The
omnisense site's [usage policies](https://glitchr-omnisense.github.io/omnisense/apis/policies) table
shows every provider's declaration, read from its published `policy.json`.

## 4. Test

- `Tests/<Name>ContractTest.php` uses the family's conformance trait (`<Core>\Testing\*ContractTests`).
  It runs offline, on recorded answers: give the suite the service's real answers (recorded
  once, or written from the service's documentation when no key is at hand - say which in the
  fixture's docblock). The compatibility dashboard of the omnisense site counts a package as
  running the suite when a test class holds `use <Core>\Testing\<Something>ContractTests;`.
- A check the service legitimately cannot meet becomes the trait's optional hook, documented -
  not a skipped test, not a fake pass. A check it fails is a defect: fix it.
- `Tests/PolicyTest.php`: the policy found by the export and valid; once read, its figures; where
  the provider counts requests itself (a token, pages, a resource), one test each: over a site's
  limit, nothing is sent.
- `Tests/NoRetryTest.php` (families with an HTTP client): a `RetryableHttpClient` over a
  `MockHttpClient`, answering 503 then 200, given to the factory - the request is sent once and
  its 503 reaches the provider. Keep it when you change the client's wiring.
- `Tests/BareTest.php` (omnisense/contracts' `BareTestCase`): every class loads in a process of
  its own with nothing from outside but the core, the kernel and what `composer.json` requires.
- Your own tests beside them: what is particular to the service (its URL, its signature, its
  paging).
- Live checks, when the service is reachable without a key or with one from the environment, go in
  the PHPUnit group `live` (excluded by default): `vendor/bin/phpunit --group live`.
- PHPStan at the highest level, tests included: `vendor/bin/phpstan analyse`. The baseline
  (`phpstan-baseline.neon`) is empty from the first commit; keep it so - fix the type, don't
  accept the error.

## 5. Document

- `README.md`: what the provider is, its smallest use, and **Verified, and not**: what was run
  (the suite, offline; the live group, on which date, against what), and what was not. Never
  claim what no test ran.
- `docs/index.md`: installation, every option with its default, what it answers and what it
  refuses, its errors, its usage policy and where it was read.
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
   a pre-release for `vX.Y.Z-rc.1` or any `0.x`. `.gitattributes` keeps the tests, the CI files
   and the analysis configuration out of that archive (and of what Composer installs).
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

## Checklist

What a maintainer, or a company publishing the provider of its own API, ticks before asking for a
review:

- [ ] Generated with `bin/init`; `composer validate`, `vendor/bin/phpunit` and
      `vendor/bin/phpstan analyse` green in `ghcr.io/glitchr-studio/php:8.4`.
- [ ] No framework in `require`; the HTTP client and caches come from outside.
- [ ] The conformance suite runs on the service's answers, recorded or written from its
      documentation, and says which.
- [ ] Unsupported contracts refused with the family's exception; every refusal of the service is
      the family's exception, with no secret in its message or trace.
- [ ] Answers read through `Data`; the PHPStan baseline empty.
- [ ] The HTTP client wrapped in `Http\NoRetry` where it comes in (or the family's factory does
      it); `Tests/NoRetryTest.php` green.
- [ ] `$c->backoff()` given to the HTTP calls; a 429 holds the next calls.
- [ ] Token requests, further pages and per-resource requests counted through the factory's
      guard, each with its test.
- [ ] The provider's terms read today at their source; `provenance.terms`/`policy` are those pages
      and `verifiedAt` that day; plans with their `source` and `verifiedAt`.
- [ ] What isn't published is unknown (`null`, `"unknown"`); figures seen elsewhere only in
      `notes`; nothing "allowed" that the terms do not allow.
- [ ] `policy.json` named in `extra.omnisense.policy`; `Tests/PolicyTest.php` asserts the figures.
- [ ] `BareTest` green.
- [ ] README's "Verified, and not" says what was run, when, against what; `docs/index.md` lists
      every option; `CHANGELOG.md` under `Unreleased`.
- [ ] A company's own package: `provenance.declaredBy` is `provider`, and `contact` a reachable
      address for questions about the terms.
