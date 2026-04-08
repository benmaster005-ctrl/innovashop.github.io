<?php

namespace App\Controller;


use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Entity\Produit;

use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ProduitRepository $produitRepository, PanierRepository $panierRepository): Response
    {   
        $user = $this->getUser();

        $produits = $produitRepository->findAll();
        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'produits' => $produits,
            'panier' => $panier
        ]);
    }
    #[Route('/product-detail/{id}', name: 'app_product_detail')]
    public function ProductDetail(Produit $produit, EntityManagerInterface $em, PanierRepository $panierRepository): Response
    {   
        $panier = $panierRepository->findOneBy(['utilisateur' => $this->getUser()]);

        return $this->render('product/productDetail.html.twig', [
            'produit' => $produit,
            'panier' => $panier
        ]);
    }
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/add-cart-product/{id}', name:'add_to_cart')]
    public function addToCart(Produit $produit, EntityManagerInterface $em, PanierRepository $panierRepository): Response
    {
         $user = $this->getUser();
        if(!$user){
            $this->redirectToRoute('app_login');
        }
        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);

        if(!$panier){
            $panier = new Panier();
            $panier->setUtilisateur($user);
            $em->persist($panier);
            $em->flush();
        }

        $panierProduit = new PanierProduit();
        $panierProduit
            ->setProduit($produit)
            ->setPanier($panier);
        $em->persist($panierProduit);
        $em->flush();

        $this->addFlash('success', 'Produit ajouté au panier!');

        return $this->render('product/productDetail.html.twig', [
            'produit' => $produit,
            'panier' => $panier
        ]);
    }
}
