<?php

// omniauth: sign-in. A password checked against an in-memory account: no account anywhere else.
// omniauth/google needs a Google OAuth client; omniauth/otp and /webauthn run on your own server.

require dirname(__DIR__).'/vendor/autoload.php';

use Omniauth\{ArrayState, Context, Omniauth};
use Omniauth\Method\Methods;
use Omniauth\Password\PasswordMethod;
use Omniauth\Testing\{InMemoryStore, TestAccount};
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

$hasher = new NativePasswordHasher();
$store = new InMemoryStore(new TestAccount('42', 'ada', 'ada@example.org', $hasher->hash('analytical-engine')));
$session = [];
$omniauth = new Omniauth(accounts: $store, methods: new Methods([new PasswordMethod($store, $store, $hasher)]), state: new ArrayState($session));

$result = $omniauth->verify('password', new Context(origin: 'https://example.org', host: 'example.org'), ['identifier' => 'ada@example.org', 'password' => 'analytical-engine']);
echo $result->status->value, ' as ', $result->account?->identifier, "\n";   // authenticated as ada
