<?php

namespace App\Controller;

use App\Repository\PanierProduitRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class CartController extends AbstractController
{   
    #[IsGranted('ROLE_USER')]
    #[Route('/cart', name: 'app_cart')]
    public function index(PanierRepository $panierRepository): Response
    {
        $user = $this->getUser();

        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);
        $products = $panier->getPanierProduits();

        $total = 0;
        foreach ($products as $product){
            $total += $product->getProduit()->getPrix();
        }
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'panier' => $panier,
            'total' => $total
        ]);
    }

    #[Route('/cart/delete/{id}', name:'remove_product')]
    public function removeProduct(int $id, PanierRepository $panierRepository, PanierProduitRepository $panierProduitRepos,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);

        $produit = $panierProduitRepos->findOneBy(['panier' => $panier, 'id' => $id]);
        
        if($produit){
            $em->remove($produit);
            $em->flush();
        }


        $this->addFlash('success', 'Panier supprimé');
        return $this->redirectToRoute('app_cart'); // ou votre route panier
    }

        


}
