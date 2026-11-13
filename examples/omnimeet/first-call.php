<?php

// omnimeet: video meetings. The direct provider runs peer to peer through your own TURN relay: no account.
// omnimeet/jitsi uses a Jitsi server (meet.jit.si or your own).

require dirname(__DIR__).'/vendor/autoload.php';

use Omnimeet\Direct\DirectGatewayFactory;
use Omnimeet\Direct\InMemorySignalStore;
use Omnimeet\Model\Meeting;
use Omnimeet\Model\Participant;
use Omnimeet\Registry;

$registry = new Registry([new DirectGatewayFactory(new InMemorySignalStore())], [
    'direct' => ['factory' => 'direct', 'options' => ['turn_secret' => 'the-relay-s-secret', 'turn_urls' => ['turn:turn.example.org:3478']]],
]);
$gateway = $registry->get('direct');
$meeting = $gateway->open(new Meeting('d9ab80f93063685d8b1b41eb74525232', new DateTimeImmutable('+5 minutes'), new DateTimeImmutable('+35 minutes')));
$access = $gateway->join($meeting, Participant::host('u42', 'Ada', 'en'));
echo $access->kind->name, ': run ', basename((string) $access->script), ' in room ', $access->options['room'], "\n";
// ENGINE: run visio.js in room d9ab80f93063685d8b1b41eb74525232
