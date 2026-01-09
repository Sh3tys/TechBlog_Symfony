<?php
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$transport = Transport::fromDsn('smtp://8967d9659722a2:3aab38ba042bab@sandbox.smtp.mailtrap.io:2525');
$mailer = new Mailer($transport);

$email = (new Email())
    ->from('test@techblog.fr')
    ->to('test@example.com')
    ->subject('Test Mailtrap')
    ->text('Ceci est un test');

try {
    $mailer->send($email);
    echo "✅ Email envoyé avec succès !\n";
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
