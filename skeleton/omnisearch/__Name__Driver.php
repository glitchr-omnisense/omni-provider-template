<?php

namespace __Namespace__;

use Omnisearch\DBAL\Driver;
use Omnisearch\DBAL\Request;
use Omnisearch\DBAL\Response;
use Omnisearch\Mapping\ClassMetadata;

/**
 * __Title__'s driver: the metadata as the engine's index, the objects as its
 * documents, a Request as its query and its answer as a Response.
 *
 * The template's driver gives each operation to $engine, omnisearch's engine
 * in the PHP process; replace each body with the engine's call, keeping
 * omnisearch's rule (docs/drivers.md): search() answers a failed Response (the
 * engine's status, its message) and never throws; a write the engine refuses,
 * or that cannot reach it, is an Omnisearch\Exception\DriverException whose
 * code is the HTTP status (503 when there was none); creating an index that is
 * there, dropping one that is not and removing an unknown identifier are no
 * errors.
 */
final class __Name__Driver implements Driver
{
    public function __construct(private readonly Driver $engine)
    {
    }

    public function create(ClassMetadata $metadata): void
    {
        $this->engine->create($metadata);
    }

    public function drop(ClassMetadata $metadata): void
    {
        $this->engine->drop($metadata);
    }

    public function schema(ClassMetadata $metadata): array
    {
        return $this->engine->schema($metadata);
    }

    public function index(ClassMetadata $metadata, object ...$objects): void
    {
        $this->engine->index($metadata, ...$objects);
    }

    public function remove(ClassMetadata $metadata, string ...$ids): void
    {
        $this->engine->remove($metadata, ...$ids);
    }

    public function search(ClassMetadata $metadata, Request $request): Response
    {
        return $this->engine->search($metadata, $request);
    }
}
