<?php

// src/Mailer/OrderMailer.php

namespace App\Mailer;

use App\Entity\Commande;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class OrderMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $shopName  = 'Ma Boutique',
        private readonly string $shopEmail = 'noreply@maboutique.fr',
        private readonly string $appUrl    = 'https://maboutique.fr',
    ) {}

    public function sendOrderRecap(Commande $commande): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->shopEmail, $this->shopName))
            ->to($commande->getUtilisateur()->getEmail())
            ->subject(sprintf('Votre commande #%d — %s', $commande->getId(), $this->shopName))
            ->htmlTemplate('email/order_recap_email.html.twig')
            ->context([
                'commande' => $commande,
                'shopName' => $this->shopName,
                'orderUrl' => sprintf('%s/commandes/%d', $this->appUrl, $commande->getId()),
            ]);

        $this->mailer->send($email);
    }
}
