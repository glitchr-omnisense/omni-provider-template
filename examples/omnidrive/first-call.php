<?php

// omnidrive: file storage. A directory of this machine needs no account; omnidrive/s3, /sftp, /gdrive... take
// the same calls with their credentials.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnidrive\Local\LocalDriveFactory;
use Omnidrive\Registry;

$root = sys_get_temp_dir().'/omnidrive-example';
$drives = new Registry([new LocalDriveFactory()], [
    'uploads' => ['factory' => 'local', 'options' => ['root' => $root, 'public_url' => '/uploads']],
]);
$drives->get('uploads')->write('notes/hello.txt', 'Hello.');
echo $drives->get('uploads')->read('notes/hello.txt'), "\n";       // Hello.
echo $drives->get('uploads')->publicUrl('notes/hello.txt'), "\n";  // /uploads/notes/hello.txt
