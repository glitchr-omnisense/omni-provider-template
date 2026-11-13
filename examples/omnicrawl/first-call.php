<?php

// omnicrawl: polite page reading. The site's answers (robots.txt, then the page) are recorded: nothing is
// fetched. In production the client is symfony/http-client's HttpClient::create(); the gate reads robots.txt
// first and names the bot. The hosted crawlers (omnicrawl/apify, /firecrawl...) need their account's key.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnicrawl\{Agent, Registry};
use Omnicrawl\Direct\DirectFactory;
use Omnicrawl\Guard\Gate;
use Omnicrawl\Testing\RecordedHttpClient;

$http = RecordedHttpClient::routes([
    '~^GET https://news\.example\.com/robots\.txt$~' => [200, "User-agent: *\nDisallow: /drafts/\n", ['content-type' => ['text/plain']]],
    '~^GET https://news\.example\.com/articles/1$~' => [200, '<html><head><title>Article</title><meta property="og:title" content="An article"></head></html>', ['content-type' => ['text/html']]],
]);
$gate = new Gate(Agent::parse('ExampleBot/1.0 (+https://www.example.org/bot; bot@example.org)'), $http);
$registry = new Registry([new DirectFactory($http)], ['preview' => ['factory' => 'direct']], $gate);

echo $registry->extract('preview')->extract('https://news.example.com/articles/1')->preview()['title'], "\n";   // An article
