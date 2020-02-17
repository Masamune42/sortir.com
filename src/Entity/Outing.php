<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OutingRepository")
 */
class Outing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Veuillez remplir ce champ")
     * @ORM\Column(type="string", length=300)
     */
    private $name;

    /**
     * @Assert\NotBlank(message="Veuillez remplir ce champ")
     * @ORM\Column(type="datetime")
     */
    private $startTime;

    /**
     * @Assert\NotBlank(message="Veuillez remplir ce champ")
     * @Assert\GreaterThanOrEqual(value=1, message="Ce champ doit être obligatoirement supérieur à 0")
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @Assert\NotBlank(message="Veuillez remplir ce champ")
     * @ORM\Column(type="datetime")
     */
    private $limitDateTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $registerMax;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $infoOuting;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Establishment", inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $establishment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Place", inversedBy="outings")
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="outingOrganized")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizer;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="outingParticipated")
     */
    private $participant;

    public function __construct()
    {
        $this->participant = new ArrayCollection();
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

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getLimitDateTime(): ?\DateTimeInterface
    {
        return $this->limitDateTime;
    }

    public function setLimitDateTime(\DateTimeInterface $limitDateTime): self
    {
        $this->limitDateTime = $limitDateTime;

        return $this;
    }

    public function getRegisterMax(): ?int
    {
        return $this->registerMax;
    }

    public function setRegisterMax(int $registerMax): self
    {
        $this->registerMax = $registerMax;

        return $this;
    }

    public function getInfoOuting(): ?string
    {
        return $this->infoOuting;
    }

    public function setInfoOuting(string $infoOuting): self
    {
        $this->infoOuting = $infoOuting;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

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

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participant->contains($participant)) {
            $this->participant[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participant->contains($participant)) {
            $this->participant->removeElement($participant);
        }

        return $this;
    }

    public function getStatusDisplayAndActions($currentUser)
    {
        $result = [
            'display' => '', // état à afficher
            'deletable' => false, //supprimer
            'cancelable' => false, //annuler
            'modifiable' => false, // modifier
            'publishable' => false, //publier
            'showable' => false, // afficher
            'registerable' => false, // s'inscrire
            'unregisterable' => false, //se desinscrire
        ];


        if ($this->status->getNameTech() == 'draft') { //Draf outing
            $result['display'] = 'En création';

            if ($currentUser == $this->organizer) {
                $result['deletable'] = true; //supprimer
                $result['modifiable'] = true; // modifier
                $result['publishable'] = true; //publier
            }
        } elseif ($this->status->getNameTech() == 'canceled') { //canceled outing
            $result['display'] = 'Annulée';

            if ($currentUser == $this->organizer || in_array($currentUser, $this->participant->toArray())) {
                $result['showable'] = true; //showable if the user is participant or organizer

            }

        } elseif ($this->status->getNameTech() == 'published') {
            $now = new \DateTime('now');
            $endTime = clone($this->startTime);
            $endTime->add(new \DateInterval('PT'.$this->duration.'M')); // endtime = start time + duration

            $result['showable'] = true;

            if ($endTime < $now) {
                $result['display'] = 'Terminée';
            } elseif ($this->startTime < $now) {
                $result['display'] = 'En cours';
            } else {
                if ($currentUser == $this->organizer) {
                    $result['cancelable'] = true;

                }

                if ($this->limitDateTime < $now) {
                    $result['display'] = 'Clôturée';

                } else {
                    if (in_array($currentUser, $this->participant->toArray())) {
                        $result['unregisterable'] = true;
                    }

                    if (count($this->participant->toArray()) < $this->registerMax) {
                        $result['display'] = 'Ouvert';
                        if (!in_array($currentUser, $this->participant->toArray())) {
                            $result['registerable'] = true;

                        }

                    } else {
                        $result['display'] = 'Complet';

                    }
                }
            }
        }

        return $result;
    }
}
