<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Beats::class)]
    private $beats;

    public function __construct()
    {
        $this->beats = new ArrayCollection();
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
            $beat->setCategory($this);
        }

        return $this;
    }

    public function removeBeat(Beats $beat): self
    {
        if ($this->beats->removeElement($beat)) {
            // set the owning side to null (unless already changed)
            if ($beat->getCategory() === $this) {
                $beat->setCategory(null);
            }
        }

        return $this;
    }
    
    public function __toString() {
        return $this->getName();
    }
}