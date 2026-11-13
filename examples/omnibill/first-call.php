<?php

// omnibill: e-invoicing. The email channel over Symfony Mailer's null transport: the invoice is "sent" and
// nothing leaves. The platforms (omnibill/chorus-pro, /pennylane, /odoo...) need an account. The PDF content
// here is a placeholder: the application provides its own conformant file.

require dirname(__DIR__).'/vendor/autoload.php';

use Omnibill\Email\EmailGatewayFactory;
use Omnibill\Model\{Invoice, Party, Syntax};
use Omnibill\Registry;
use Symfony\Component\Mailer\{Mailer, Transport};

$mailer = new Mailer(Transport::fromDsn('null://null'));
$registry = new Registry([new EmailGatewayFactory($mailer)], [
    'invoices' => ['factory' => 'email', 'options' => ['from' => 'Workshop <invoices@example.org>']],
]);
$flow = $registry->get('invoices')->submit(new Invoice(
    content: "%PDF-1.7\n...", filename: 'F-2026-042.pdf', syntax: Syntax::FACTURX, number: 'F-2026-042',
    seller: new Party('Workshop', siren: '123456789'), buyer: new Party('Example House', siren: '702042755', email: 'accounts@example.org'), total: '1250.00',
));
echo $flow->invoiceNumber, ': ', $flow->status->label(), "\n";   // F-2026-042: Envoyée
