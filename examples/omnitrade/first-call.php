<?php

// omnitrade: payments. PayPal's two answers are recorded, so nothing leaves the process; a real call needs a
// PayPal (sandbox) account's client id and secret, and no MockHttpClient.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnitrade\Model\Money;
use Omnitrade\Model\Payment;
use Omnitrade\PayPal\PayPalGatewayFactory;
use Omnitrade\Registry;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

$paypal = new MockHttpClient([
    new MockResponse('{"access_token": "not-a-token", "token_type": "Bearer", "expires_in": 32400}'),
    new MockResponse('{"id": "5O190127TN364715T", "status": "PAYER_ACTION_REQUIRED", "links": [{"rel": "payer-action", "href": "https://www.sandbox.paypal.com/checkoutnow?token=5O190127TN364715T"}]}'),
]);
$registry = new Registry([new PayPalGatewayFactory($paypal)], [
    'paypal' => ['factory' => 'paypal', 'options' => ['client_id' => 'id', 'secret' => 'secret', 'sandbox' => true]],
]);

$transaction = $registry->get('paypal')->purchase(new Payment(amount: Money::of(1250, 'EUR'), reference: 'ORDER-1042', returnUrl: 'https://shop.example/back'));
echo $transaction->status->value, ' ', $transaction->redirectUrl, "\n";   // pending https://www.sandbox.paypal.com/checkoutnow?token=5O190127TN364715T
