<?php

// omnisearch: full-text search over your objects. The memory driver indexes in this process: no server.
// omnisearch/typesense takes the same calls with a Typesense server of your own.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnisearch\Mapping\{AttributeLoader, ClassMetadata, Field, Index, MetadataRegistry, Type};
use Omnisearch\Memory\MemoryDriverFactory;
use Omnisearch\ORM\{Indexer, Manager, ObjectStore, Query};
use Omnisearch\Registry;

#[Index('note')]
final class Note
{
    public function __construct(public readonly string $id, #[Field(infix: true)] public readonly string $text, #[Field(type: Type::INT, sort: true)] public readonly int $rank)
    {
    }
}

final class Notes implements ObjectStore   // where the notes live: an array here, a database in an application
{
    public array $notes = [];

    public function identify(object $object): string { return $object->id; }

    public function load(ClassMetadata $metadata, array $ids, bool $cacheable = false): array
    {
        return array_values(array_filter(array_map(fn ($id) => $this->notes[$id] ?? null, $ids)));
    }
}

$store = new Notes();
$manager = new Manager(new Registry([new MemoryDriverFactory($store)], ['default' => ['driver' => 'memory']]), new MetadataRegistry([AttributeLoader::load(Note::class)]), $store);
$manager->create();
$indexer = new Indexer($manager);
foreach ([new Note('1', 'Mount Fuji at dawn', 3), new Note('2', 'Fujisan in music', 1), new Note('3', 'A recipe for ramen', 2)] as $note) {
    $indexer->persisted($store->notes[$note->id] = $note);
}
$indexer->flush();

foreach ($manager->finder(Note::class)->search((new Query('fuji'))->in('text')->sortBy('rank', 'desc'))->objects() as $note) {
    echo $note->id, ' ', $note->text, "\n";   // 1 Mount Fuji at dawn, then 2 Fujisan in music
}
