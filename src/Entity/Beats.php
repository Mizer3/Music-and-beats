<?php

namespace App\Entity;

use App\Repository\BeatsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BeatsRepository::class)]
class Beats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'integer')]
    private $price;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $imageName;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'string', length: 100)]
    private $beatName;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'beats')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'beats')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\ManyToOne(targetEntity: Home::class, inversedBy: 'beats')]
    private $home;

    #[ORM\ManyToMany(targetEntity: OrderBeats::class, mappedBy: 'beats')]
    private $orderBeats;

    #[ORM\Column(type: 'boolean')]
    private $isVIP = false;

    public function __construct()
    {
        $this->orderBeats = new ArrayCollection();
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBeatName(): ?string
    {
        return $this->beatName;
    }

    public function setBeatName(string $beatName): self
    {
        $this->beatName = $beatName;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getHome(): ?Home
    {
        return $this->home;
    }

    public function setHome(?Home $home): self
    {
        $this->home = $home;

        return $this;
    }

    /**
     * @return Collection<int, OrderBeats>
     */
    public function getOrderBeats(): Collection
    {
        return $this->orderBeats;
    }

    public function addOrderBeat(OrderBeats $orderBeat): self
    {
        if (!$this->orderBeats->contains($orderBeat)) {
            $this->orderBeats[] = $orderBeat;
            $orderBeat->addBeat($this);
        }

        return $this;
    }

    public function removeOrderBeat(OrderBeats $orderBeat): self
    {
        if ($this->orderBeats->removeElement($orderBeat)) {
            $orderBeat->removeBeat($this);
        }

        return $this;
    }

    public function getIsVIP(): ?bool
    {
        return $this->isVIP;
    }

    public function setIsVIP(bool $isVIP): self
    {
        $this->isVIP = $isVIP;

        return $this;
    }
}