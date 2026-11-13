<?php

// omnisign: e-signature. DocuSeal's answer to the creation is recorded: nothing leaves the process. A real call
// needs a DocuSeal instance of your own (or an account) and its API key.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnisign\Docuseal\DocusealGatewayFactory;
use Omnisign\Model\{Envelope, Signer};
use Omnisign\Registry;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

$http = new MockHttpClient(new MockResponse('[{"id":11,"submission_id":4,"external_id":"tenant","status":"awaiting","slug":"abc"}]'));
$registry = new Registry([new DocusealGatewayFactory($http)], [
    'contracts' => ['factory' => 'docuseal', 'options' => ['url' => 'https://sign.example.org/api', 'api_key' => 'not-a-real-key']],
]);
$gateway = $registry->get('contracts');
$envelope = $gateway->create(new Envelope('Lease', signers: [new Signer('tenant', 'Camille Durand', 'camille@example.org')], embedded: true, template: '1'));
echo $envelope->reference, ' ', $envelope->status->value, "\n";      // 4 sent
echo $gateway->signingUrl($envelope, 'tenant')->url, "\n";          // https://sign.example.org/s/abc
