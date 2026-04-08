<?php

namespace App\Controller;

use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $produitRepository, PanierRepository $panierRepository): Response
    {
        $user = $this->getUser();

        $produits = $produitRepository->findAll();
        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'produits' => $produits,
            'panier' => $panier
        ]);
    }
}
