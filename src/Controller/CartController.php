<?php

namespace App\Controller;

use App\Form\CartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartManager $cartManager, Request $request): Response
    {
        $cartItems = $cartManager->getCurrentCart(); // Récupère les articles actuellement dans le panier
        $form = $this->createForm(CartType::class);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $cartManager->saveCart(); // Sauvegarde le panier mis à jour dans la session
            return $this->redirectToRoute('cart');

        }

        dd($cartItems);

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'form' => $form->createView(),
        ]);
    }
}
