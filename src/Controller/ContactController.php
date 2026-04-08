<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, PanierRepository $panierRepository): Response
    {
        $user = $this->getUser();

        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);


        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'panier' => $panier
        ]);
    }
}
