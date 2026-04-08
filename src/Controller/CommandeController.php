<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CheckoutType;
use App\Mailer\OrderMailer;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(PanierRepository $panierRepos, Request $request, EntityManagerInterface $em, OrderMailer $mailer): Response
    {
        $user = $this->getUser();
        $panier = $panierRepos->findOneBy(['utilisateur' => $user]);

        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);
        
        $products = $panier->getPanierProduits();

        $total = 0;
        foreach ($products as $product){
            $total += $product->getProduit()->getPrix();
        }
        if($form->isSubmitted() && $form->isValid()){
            $commande = new Commande;
            $commande
                ->setTotal($total)
                ->setStatus('confirme')
                ->setProduit($product->getProduit())

                ->setDate(new \DateTime())
                ->setUtilisateur($user);
                
            
            $em->persist($commande);
            $em->flush();  
            
            //$mailer->sendOrderRecap($commande);

            return $this->redirectToRoute('app_product');
            
        }
        
        return $this->render('checkout/index.html.twig', [
            'panier' => $panier,
            'form' => $form,
            'total' => $total,
        ]);
    
    }
}
