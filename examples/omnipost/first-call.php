<?php

// omnipost: sharing and publishing. Share links need no account; publishing (omnipost/instagram, /youtube)
// needs the platform's app and the account's consent.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnipost\Share\Registry;
use Omnipost\Share\Shareable;

$share = Registry::defaults()->share(new Shareable('https://example.com/articles/fuji', 'Mount Fuji at dawn'), ['x', 'email']);
echo $share->url('x'), "\n";   // https://twitter.com/intent/tweet?url=https%3A%2F%2Fexample.com%2Farticles%2Ffuji&text=Mount%20Fuji%20at%20dawn
