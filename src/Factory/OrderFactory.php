<?php

namespace App\Factory;
use App\Entity\ShopProduct;
use App\Entity\ShopProductColor;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class OrderFactory
 */
class OrderFactory
{
    private $entityManager;
    private $session;
    private $cart = [];

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;

        // Récupérer le panier stocké en session (s'il existe)
        $this->cart = $this->session->get('cart', []);
    }

    public function clearCart()
    {
        $this->cart = []; // Vider complètement le panier

        // Sauvegarder le panier vide dans la session
        $this->saveCartSession();
    }

    public function removeItem(int $productId)
    {
        // Rechercher l'index de l'élément correspondant dans le panier en utilisant l'ID du produit
        $itemIndex = $this->findItemIndex($productId);

        // Si l'élément existe dans le panier, le supprimer
        if ($itemIndex !== false) {
            unset($this->cart[$itemIndex]);
            // Réorganiser les clés du tableau pour éviter les clés numériques manquantes
            $this->cart = array_values($this->cart);

            // Sauvegarder le panier mis à jour dans la session
            $this->saveCartSession();
        }
    }

    // Méthode pour rechercher l'index d'un élément dans le panier en utilisant l'ID du produit
    private function findItemIndex(int $productId): int|false
    {
        foreach ($this->cart as $index => $item) {
            if ($item['shopProduct']->getId() === $productId) {
                return $index;
            }
        }

        return false; // L'élément n'a pas été trouvé dans le panier
    }

    public function createItem(int $productId, Request $request)
    {
        // Récupérer l'entité ShopProduct correspondante à partir de l'ID
    $shopProduct = $this->entityManager->getRepository(ShopProduct::class)->find($productId);

    // Vérifier que le produit existe avant de continuer
    if (!$shopProduct) {
        throw new \InvalidArgumentException('Produit non trouvé.');
    }
     // Récupérer les paramètres de la couleur et de la taille
     $colorId = $request->request->get('color');
     // Récupérer l'entité Color correspondante à partir de la base de données
     $color = $this->entityManager->getRepository(ShopProductColor::class)->find($colorId);

     if ($color) {
         // Récupérer le nom de la couleur
         $colorName = $color->getName();
     } else {
         $colorName = null;
     }

     $size = $request->request->get('size');

     // Utiliser une clé unique pour identifier l'élément dans le panier
     $itemKey = $productId . '_' . $colorId . '_' . $size;

     // Vérifier si l'élément existe déjà dans le panier
     if (isset($this->cart[$itemKey])) {
         // Si oui, incrémente simplement sa quantité
         $this->cart[$itemKey]['quantity']++;
     } else {
         // Sinon, ajoute un nouvel OrderItem au panier
         $this->cart[$itemKey] = [
             'shopProduct' => $shopProduct,
             'quantity' => 1,
             'color' => $colorName,
             'size' => $size,
         ];
     }

        // Sauvegarder les modification du panier dans la session
        $this->saveCartSession();
        
    }

    private function saveCartSession()
    {
        $this->session->set('cart', $this->cart);
    }

    public function getCurrentOrderItems(): array
    {
        return $this->cart;
    }
}