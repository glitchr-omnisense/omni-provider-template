<?php

namespace __Namespace__;

use Omnisearch\DBAL\Driver;
use Omnisearch\DBAL\DriverFactory;
use Omnisearch\Memory\Identity;
use Omnisearch\Memory\MemoryDriver;
use Omnisearch\ORM\ObjectStore;
use Omnisearch\ORM\Transformer;

/**
 * __Title__'s drivers, from a connection's options:
 *
 *   (new __Name__Factory())->create([]);
 *
 * The template's driver hands every operation to omnisearch's engine in the
 * PHP process (Omnisearch\Memory\MemoryDriver), the smallest one the
 * conformance suite accepts. To make it the engine's, read the connection's
 * options here (its address, its key; a missing one an
 * Omnisearch\Exception\InvalidConfigException naming it), give the driver the
 * engine's client in place of the memory engine - the HTTP client is the
 * application's, given to this factory's constructor - and list the required
 * options in the contract test's requiredOptions().
 */
final class __Name__Factory implements DriverFactory
{
    public function __construct(private readonly ObjectStore $store = new Identity())
    {
    }

    public function name(): string
    {
        return '__name__';
    }

    public function create(array $options): Driver
    {
        return new __Name__Driver(new MemoryDriver(new Transformer($this->store)));
    }
}
