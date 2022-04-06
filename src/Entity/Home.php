<?php

namespace App\Entity;

use App\Repository\HomeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HomeRepository::class)]
class Home
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\OneToMany(mappedBy: 'home', targetEntity: Beats::class)]
    private $beats;

    #[ORM\OneToMany(mappedBy: 'home', targetEntity: User::class)]
    private $beatmaker;

    public function __construct()
    {
        $this->beats = new ArrayCollection();
        $this->beatmaker = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
            $beat->setHome($this);
        }

        return $this;
    }

    public function removeBeat(Beats $beat): self
    {
        if ($this->beats->removeElement($beat)) {
            // set the owning side to null (unless already changed)
            if ($beat->getHome() === $this) {
                $beat->setHome(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getBeatmaker(): Collection
    {
        return $this->beatmaker;
    }

    public function addBeatmaker(User $beatmaker): self
    {
        if (!$this->beatmaker->contains($beatmaker)) {
            $this->beatmaker[] = $beatmaker;
            $beatmaker->setHome($this);
        }

        return $this;
    }

    public function removeBeatmaker(User $beatmaker): self
    {
        if ($this->beatmaker->removeElement($beatmaker)) {
            // set the owning side to null (unless already changed)
            if ($beatmaker->getHome() === $this) {
                $beatmaker->setHome(null);
            }
        }

        return $this;
    }
}
