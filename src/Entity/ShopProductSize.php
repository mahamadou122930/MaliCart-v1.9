<?php

namespace App\Entity;

use App\Repository\ShopProductSizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShopProductSizeRepository::class)]
class ShopProductSize
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: ShopProduct::class, mappedBy: 'size', cascade: ["persist", "remove"])]
    private Collection $shopProducts;

    public function __construct()
    {
        $this->shopProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
    
    /**
     * @return Collection<int, ShopProduct>
     */
    public function getShopProducts(): Collection
    {
        return $this->shopProducts;
    }

    public function addShopProduct(ShopProduct $shopProduct): self
    {
        if (!$this->shopProducts->contains($shopProduct)) {
            $this->shopProducts->add($shopProduct);
            $shopProduct->addSize($this);
        }

        return $this;
    }

    public function removeShopProduct(ShopProduct $shopProduct): self
    {
        if ($this->shopProducts->removeElement($shopProduct)) {
            $shopProduct->removeSize($this);
        }

        return $this;
    }
}
