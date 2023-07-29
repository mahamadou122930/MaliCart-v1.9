<?php

namespace App\Manager;

use App\Factory\OrderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartManager
{

    private $orderFactory;
    private $session;

    public function __construct(OrderFactory $orderFactory, SessionInterface $session)
    {
        $this->orderFactory = $orderFactory;
        $this->session = $session;
    }

    public function addItem(int $productId, Request $request)
    {
        // Appel à la méthode createItem du OrderFactory pour ajouter l'article au panier
        $this->orderFactory->createItem($productId, $request);
    }

    public function removeItem(int $productId)
    {
        // Appel à la méthode removeItem du OrderFactory pour supprimer l'article du panier
        $this->orderFactory->removeItem($productId);
    }

    public function clearCart()
    {
        // Appel à la méthode clearCart du OrderFactory pour vider entièrement le panier
        $this->orderFactory->clearCart();
    }

    public function getCurrentCartItems(): array
    {
        // Récupérer les articles actuellement présents dans le panier en utilisant le OrderFactory
        return $this->orderFactory->getCurrentOrderItems();
    }

    public function getTotalForProduct(int $productId): float
    {
        $cartItems = $this->getCurrentCartItems();

        // Rechercher l'article correspondant dans le panier en utilisant l'ID du produit
        $total = 0.0;
        foreach ($cartItems as $item) {
            if ($item['shopProduct']->getId() === $productId) {
                // Calculer le total pour cet article (quantité * prix unitaire)
                $total = $item['quantity'] * $item['shopProduct']->getPrice();
                break;
            }
        }

        return $total;
    }
    
}   