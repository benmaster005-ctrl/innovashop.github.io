<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/user', name: 'app_user')]
    public function index(PanierRepository $panierRepository, CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();

        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);
        $commandes = $commandeRepository->findBy(['utilisateur' => $user]);
        

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'panier' => $panier,
            'commandes' => $commandes
        ]);
    }
}
