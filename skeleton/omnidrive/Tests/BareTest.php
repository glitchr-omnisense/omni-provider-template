<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use Omnisense\Contracts\Testing\BareTestCase;

/**
 * __package__ with no framework (omnisense/contracts' bare check): every
 * class of the package loaded in a PHP process of its own, and nothing
 * loaded from outside the package but __core__, omnisense/contracts, the
 * league adapter and what composer.json requires - no framework, no
 * application, even where the tests' dependencies are installed beside it.
 * A drive is a league/flysystem adapter: League\Flysystem is allowed.
 */
final class BareTest extends BareTestCase
{
    protected static function entryClass(): string
    {
        return __Name__Factory::class;
    }

    /** What composer.json's require names, and league/flysystem, which __core__ builds every drive on. */
    protected static function requiredPackages(): array
    {
        $composer = json_decode((string) file_get_contents(\dirname(__DIR__).'/composer.json'), true, 512, \JSON_THROW_ON_ERROR);
        $require = \is_array($composer) && \is_array($composer['require'] ?? null) ? $composer['require'] : [];

        return [...array_values(array_filter(array_map(strval(...), array_keys($require)), static fn (string $package) => str_contains($package, '/'))), 'league/flysystem'];
    }

    protected static function allowedNamespaces(): array
    {
        return ['League\\Flysystem\\'];
    }

    /** The core's folder too, wherever it is installed (a path repository's checkout in a harness). */
    protected static function coreDirs(): array
    {
        return [...parent::coreDirs(), self::dirOf(\__core_class__::class)];
    }
}
