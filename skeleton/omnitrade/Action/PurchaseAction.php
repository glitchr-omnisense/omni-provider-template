<?php

namespace __Namespace__\Action;

use __Namespace__\Api;
use Omnitrade\Action\ActionInterface;
use Omnitrade\Action\ApiAwareInterface;
use Omnitrade\Action\ApiAwareTrait;
use Omnitrade\Request\Purchase;
use Omnitrade\Request\Request;

/**
 * A payment taken on the provider's hosted page: POST payments, the buyer then
 * sent to its checkout_url (a PENDING Transaction). Replace the path and the
 * fields with the service's.
 */
final class PurchaseAction implements ActionInterface, ApiAwareInterface
{
    /** @use ApiAwareTrait<Api> */
    use ApiAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    public function supports(Request $request): bool
    {
        return $request instanceof Purchase;
    }

    public function execute(Request $request): void
    {
        \assert($request instanceof Purchase);
        $payment = $request->payment;

        $request->setResult($this->api->transaction($this->api->request('POST', 'payments', [
            'json' => array_filter([
                'amount' => $payment->amount->amount,
                'currency' => $payment->amount->currency,
                'reference' => $payment->reference,
                'description' => $payment->description,
                'email' => $payment->customer?->email,
                'return_url' => $payment->returnUrl,
                'cancel_url' => $payment->cancelUrl,
            ], static fn (mixed $value) => null !== $value),
            'headers' => null !== $payment->idempotencyKey ? ['Idempotency-Key' => $payment->idempotencyKey] : [],
        ])));
    }
}
