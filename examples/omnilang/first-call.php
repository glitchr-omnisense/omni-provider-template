<?php

// omnilang: translation. DeepL's answer is recorded: nothing leaves the process. A real call needs a DeepL key
// (a free one ends in ":fx"); omnilang/libretranslate can run on your own server without one.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnilang\Deepl\DeeplFactory;
use Omnilang\Registry;
use Omnilang\Testing\RecordedHttpClient;

$http = RecordedHttpClient::routes([
    '~^POST https://api-free\.deepl\.com/v2/translate$~' => [200, '{"translations": [{"detected_source_language": "FR", "text": "Double room with a view"}]}'],
]);
$lang = new Registry([new DeeplFactory($http)], ['deepl' => ['factory' => 'deepl', 'options' => ['key' => 'a-free-key:fx']]]);

echo $lang->translateText('Chambre double avec vue', 'en')->text, "\n";   // Double room with a view
