<?php

namespace __Namespace__\Tests;

use __Namespace__\__Name__Factory;
use PHPUnit\Framework\TestCase;

/**
 * __package__ with no framework: every class of the package loaded in a PHP
 * process of its own, and no class of a framework, an HTTP client or an
 * application among what that loaded.
 */
final class BareTest extends TestCase
{
    private const FOREIGN = '~^(?:Symfony\\\\(?!Contracts\\\\|Polyfill\\\\)|(?:Illuminate|Laravel|Doctrine|Twig|GuzzleHttp|Omnibridge|Base|App)\\\\)~';

    public function testEveryClassLoadsWithoutAFramework(): void
    {
        $autoload = null;
        foreach (\Composer\Autoload\ClassLoader::getRegisteredLoaders() as $vendor => $loader) {
            if ($loader->findFile(__Name__Factory::class)) {
                $autoload = $vendor.'/autoload.php';
            }
        }
        self::assertNotNull($autoload);
        $process = proc_open([\PHP_BINARY, __DIR__.'/Fixtures/load-all.php', $autoload, \dirname(__DIR__)], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        self::assertIsResource($process);
        $out = (string) stream_get_contents($pipes[1]);
        $err = (string) stream_get_contents($pipes[2]);
        self::assertSame(0, proc_close($process), $err.$out);
        $report = json_decode($out, true);
        self::assertIsArray($report, $err.$out);
        self::assertIsArray($report['loaded']);
        self::assertIsArray($report['symbols']);

        self::assertContains(__Name__Factory::class, $report['loaded']);
        self::assertSame([], array_values(preg_grep(self::FOREIGN, array_filter($report['symbols'], 'is_string')) ?: []), 'no class of a framework, an HTTP client or an application');
    }
}
