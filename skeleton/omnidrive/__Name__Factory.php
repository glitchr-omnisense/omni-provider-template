<?php

namespace __Namespace__;

use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Omnidrive\Config;
use Omnidrive\Drive;
use Omnidrive\Flysystem\FlysystemDriveFactory;

/**
 * __Title__ as an omnidrive drive, from the options every drive takes
 * (Omnidrive\Flysystem\FlysystemDriveFactory):
 *
 *   public_url            where its files are served from, a prefix or a list of them
 *   visibility            what a file is written with by default: public, private
 *   directory_visibility  the same for a directory
 *   retain_visibility     whether a copy keeps its source's visibility (true)
 *
 *   (new __Name__Factory())->create(['public_url' => 'https://files.example.org']);
 *
 * The template's engine keeps the files in this process's memory
 * (league/flysystem-memory): the smallest one the conformance suite accepts.
 * To make it the service's, build the service's league adapter in build() in
 * place of InMemoryFilesystemAdapter - its client given to this factory's
 * constructor, never created inside - declare the service's options and their
 * defaults in populate(), name the ones it cannot do without in
 * omnidrive.required_options, and replace league/flysystem-memory in
 * composer.json with the adapter's package.
 */
final class __Name__Factory extends FlysystemDriveFactory
{
    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnidrive.factory_name' => '__name__',
            'omnidrive.factory_title' => '__Title__',
            'omnidrive.required_options' => [],
        ]);
    }

    protected function build(Config $c, string $name): Drive
    {
        return $this->drive($name, new InMemoryFilesystemAdapter(), $c);
    }
}
