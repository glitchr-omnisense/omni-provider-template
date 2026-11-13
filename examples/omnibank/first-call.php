<?php

// omnibank: bank access. A bank's CSV export is read offline: no bank account to connect, no network.
// omnibank/powens, /bridge, /qonto connect to a bank with an aggregator's or the bank's credentials.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnibank\Files\FilesGatewayFactory;
use Omnibank\Model\Connection;
use Omnibank\Registry;

$csv = "Date;Libellé;Montant\n01/09/2026;LOYER SEPTEMBRE;-850,00\n05/09/2026;VIREMENT CLIENT;2 400,00\n";

$registry = new Registry([new FilesGatewayFactory()], ['statements' => ['factory' => 'files']]);
$gateway = $registry->get('statements');
$connection = new Connection(['statements' => [['name' => 'export.csv', 'content' => $csv]]]);

foreach ($gateway->accounts($connection) as $account) {
    foreach ($gateway->transactions($connection, $account) as $transaction) {
        echo $transaction->bookedOn->format('Y-m-d'), ' ', $transaction->amount->decimal(), ' ', $transaction->label, "\n";
    }
}
// 2026-09-01 -850.00 LOYER SEPTEMBRE
// 2026-09-05 2400.00 VIREMENT CLIENT
