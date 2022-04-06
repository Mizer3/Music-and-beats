<?php

namespace App\Entity;

use App\Repository\OrderBeatsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderBeatsRepository::class)]
class OrderBeats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToMany(targetEntity: Beats::class, inversedBy: 'orderBeats')]
    private $beats;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $totalPrice;
    
    public function __construct()
    {
        $this->beats = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(?int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * @return Collection<int, Beats>
     */
    public function getBeats(): Collection
    {
        return $this->beats;
    }

    public function addBeat(Beats $beat): self
    {
        if (!$this->beats->contains($beat)) {
            $this->beats[] = $beat;
        }
        
        return $this;
    }

    public function removeBeat(Beats $beat): self
    {
        $this->beats->removeElement($beat);

        return $this;
    }
}
