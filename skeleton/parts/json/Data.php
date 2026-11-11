<?php

namespace __Namespace__;

use Omnisense\Contracts\Http\Data as KernelData;

/**
 * __Title__'s decoded answers, read without trusting their shape: what a
 * provider does with PHPStan at its highest level, rather than casting a
 * mixed value blindly. The kernel's readers (omnisense/contracts'
 * Http\Data) take a key or a path ("tracking.events") and give their type
 * or null: string() (trimmed, "" as null), int(), float(), bool(), data()
 * (a nested object), list() (its objects), strings(), dateTime(), date().
 * Those below give a default instead, and the static ones read a value
 * already taken out of an answer.
 *
 *   $read = Data::of(json_decode($body, true));
 *   $id = $read->string('id') ?? throw new ...('A payment without its id.');
 *   foreach ($read->list('events') as $event) { $event->string('status'); }
 *
 * @internal
 */
final readonly class Data extends KernelData
{
    /** The value as text: "" when absent or not a scalar. */
    public function text(string|int $key): string
    {
        return self::cast($this->get($key));
    }

    /** The value as an integer: 0 when absent or not a scalar. */
    public function number(string|int $key): int
    {
        return self::integer($this->get($key));
    }

    /**
     * An object's fields (an empty array when it is not one).
     *
     * @return array<string, mixed>
     */
    public function fields(string|int $key): array
    {
        return self::map($this->get($key));
    }

    /** A scalar or a Stringable as text, "" for anything else. */
    public static function cast(mixed $value): string
    {
        return \is_scalar($value) || $value instanceof \Stringable ? (string) $value : '';
    }

    /** A scalar as an integer, 0 for anything else. */
    public static function integer(mixed $value): int
    {
        return \is_scalar($value) ? (int) $value : 0;
    }

    /**
     * A decoded JSON object as it is, its keys as strings (an empty array
     * when it is not an array).
     *
     * @return array<string, mixed>
     */
    public static function map(mixed $value): array
    {
        if (!\is_array($value)) {
            return [];
        }
        $map = [];
        foreach ($value as $key => $item) {
            $map[(string) $key] = $item;
        }

        return $map;
    }

    /**
     * The scalars of a list, each as cast() makes it (what is not a scalar is left out).
     *
     * @return list<string>
     */
    public static function texts(mixed $value): array
    {
        return array_values(array_map(self::cast(...), array_filter(\is_array($value) ? $value : [], 'is_scalar')));
    }
}
