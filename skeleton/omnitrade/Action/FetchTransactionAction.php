<?php

namespace __Namespace__\Action;

use __Namespace__\Api;
use Omnitrade\Action\ActionInterface;
use Omnitrade\Action\ApiAwareInterface;
use Omnitrade\Action\ApiAwareTrait;
use Omnitrade\Request\FetchTransaction;
use Omnitrade\Request\Request;

/** Where a payment stands now: GET payments/{id}. Replace the path with the service's. */
final class FetchTransactionAction implements ActionInterface, ApiAwareInterface
{
    /** @use ApiAwareTrait<Api> */
    use ApiAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    public function supports(Request $request): bool
    {
        return $request instanceof FetchTransaction;
    }

    public function execute(Request $request): void
    {
        \assert($request instanceof FetchTransaction);

        $request->setResult($this->api->transaction($this->api->request('GET', 'payments/'.rawurlencode($request->reference))));
    }
}
