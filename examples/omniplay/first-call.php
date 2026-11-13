<?php

// omniplay: game services. Tickets that a game server checks, signed with a secret the servers share: no
// account. The stores and hosts (omniplay/steam, /itch, /edgegap...) need their keys.

require dirname(__DIR__).'/vendor/autoload.php';

use Omniplay\Model\Service;
use Omniplay\Secret\FixedSecrets;
use Omniplay\Tickets;

$tickets = new Tickets(new FixedSecrets(['GAME_TICKET_SECRET' => 'a secret the game servers share']));
$game = new Service('game', secret: 'GAME_TICKET_SECRET', ttl: 60);
$ticket = $tickets->mint($game, 'server-1', ['sub' => 'player-7']);                          // what the player presents
echo json_encode($tickets->verify($game, $ticket->token, 'server-1')?->claims), "\n";    // {"sub":"player-7"}: server-1 lets the player in
var_dump($tickets->verify($game, $ticket->token, 'server-2'));                            // NULL: another server refuses it
