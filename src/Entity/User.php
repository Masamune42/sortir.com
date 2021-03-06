<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="Ce pseudo existe déjà")
 * @UniqueEntity(fields={"mail"}, message="Cet email existe déjà")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Veuillez remplir ce champ")
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @Assert\NotBlank(message="Veuillez remplir ce champ")
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @Assert\NotBlank(message="Veuillez remplir ce champ")
     * @ORM\Column(type="string", length=100)
     */
    private $firstname;

    /**
     * @Assert\NotBlank(message="Veuillez remplir ce champ")
     * @ORM\Column(type="string", length=10)
     */
    private $phone;

    /**
     * @Assert\NotBlank(message="Veuillez remplir ce champ")
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $administrator;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Establishment", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $establishment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="organizer")
     */
    private $outingOrganized;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Outing", mappedBy="participant")
     */
    private $outingParticipated;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $picture;

    /**
     * @var string token for when the user forgot his password
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected  $resetToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Group", mappedBy="creator", orphanRemoval=true)
     */
    private $groupsCreated;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="participants")
     */
    private $groupsJoined;



    public function __construct()
    {
        $this->outingOrganized = new ArrayCollection();
        $this->outingParticipated = new ArrayCollection();
        $this->groupsCreated = new ArrayCollection();
        $this->groupsJoined = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAdministrator(): ?bool
    {
        return $this->administrator;
    }

    public function setAdministrator(bool $administrator): self
    {
        $this->administrator = $administrator;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getEstablishment(): ?Establishment
    {
        return $this->establishment;
    }

    public function setEstablishment(?Establishment $establishment): self
    {
        $this->establishment = $establishment;

        return $this;
    }


    /**
     * @return Collection|Outing[]
     */
    public function getOutingOrganized(): Collection
    {
        return $this->outingOrganized;
    }

    public function addOutingOrganized(Outing $outingOrganized): self
    {
        if (!$this->outingOrganized->contains($outingOrganized)) {
            $this->outingOrganized[] = $outingOrganized;
            $outingOrganized->setOrganizer($this);
        }

        return $this;
    }

    public function removeOutingOrganized(Outing $outingOrganized): self
    {
        if ($this->outingOrganized->contains($outingOrganized)) {
            $this->outingOrganized->removeElement($outingOrganized);
            // set the owning side to null (unless already changed)
            if ($outingOrganized->getOrganizer() === $this) {
                $outingOrganized->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Outing[]
     */
    public function getOutingParticipated(): Collection
    {
        return $this->outingParticipated;
    }

    public function addOutingParticipated(Outing $outingParticipated): self
    {
        if (!$this->outingParticipated->contains($outingParticipated)) {
            $this->outingParticipated[] = $outingParticipated;
            $outingParticipated->addParticipant($this);
        }

        return $this;
    }

    public function removeOutingParticipated(Outing $outingParticipated): self
    {
        if ($this->outingParticipated->contains($outingParticipated)) {
            $this->outingParticipated->removeElement($outingParticipated);
            $outingParticipated->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        if ($this->administrator){
            return["ROLE_ADMIN"];
        }elseif ($this->getActive()) {
            return ["ROLE_USER"];
        } else {
            return ["ROLE_DEACTIVATED"];
        }

        // TODO: Implement getRoles() method.
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
<<<<<<< HEAD
     * @return string
     */
    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    /**
     * @param string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->resetToken = $resetToken;
    }
        /**
        * @return Collection|Group[]
     */

    public function getGroupsCreated(): Collection
    {
        return $this->groupsCreated;
    }

    public function addGroupsCreated(Group $groupsCreated): self
    {
        if (!$this->groupsCreated->contains($groupsCreated)) {
            $this->groupsCreated[] = $groupsCreated;
            $groupsCreated->setCreator($this);
        }

        return $this;
    }

    public function removeGroupsCreated(Group $groupsCreated): self
    {
        if ($this->groupsCreated->contains($groupsCreated)) {
            $this->groupsCreated->removeElement($groupsCreated);
            // set the owning side to null (unless already changed)
            if ($groupsCreated->getCreator() === $this) {
                $groupsCreated->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroupsJoined(): Collection
    {
        return $this->groupsJoined;
    }

    public function addGroupsJoined(Group $groupsJoined): self
    {
        if (!$this->groupsJoined->contains($groupsJoined)) {
            $this->groupsJoined[] = $groupsJoined;
            $groupsJoined->addParticipant($this);
        }

        return $this;
    }

    public function removeGroupsJoined(Group $groupsJoined): self
    {
        if ($this->groupsJoined->contains($groupsJoined)) {
            $this->groupsJoined->removeElement($groupsJoined);
            $groupsJoined->removeParticipant($this);
        }

        return $this;
    }

}
