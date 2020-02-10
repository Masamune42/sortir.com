<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EstablishmentRepository")
 */
class Establishment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="establishment")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="establishment")
     */
    private $outings;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->outings = new ArrayCollection();
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
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setEstablishment($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getEstablishment() === $this) {
                $user->setEstablishment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Outing[]
     */
    public function getOutings(): Collection
    {
        return $this->outings;
    }

    public function addOuting(Outing $outing): self
    {
        if (!$this->outings->contains($outing)) {
            $this->outings[] = $outing;
            $outing->setEstablishment($this);
        }

        return $this;
    }

    public function removeOuting(Outing $outing): self
    {
        if ($this->outings->contains($outing)) {
            $this->outings->removeElement($outing);
            // set the owning side to null (unless already changed)
            if ($outing->getEstablishment() === $this) {
                $outing->setEstablishment(null);
            }
        }

        return $this;
    }
}
