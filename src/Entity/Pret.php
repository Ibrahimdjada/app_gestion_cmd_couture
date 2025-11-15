<?php

namespace App\Entity;

use App\Repository\PretRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PretRepository::class)]
class Pret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $mntP = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datP = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datEch = null;
    
    #[ORM\Column]
    private ?float $prd = null;

    #[ORM\Column]
    private ?float $ms = null;

    #[ORM\Column(length: 255)]
    private ?string $stat = null;

    #[ORM\Column(type:'float', nullable:true)]
    private ?float $reliquat = null;

    #[ORM\Column(type:'float', nullable:true)]
    private ?float $reste = null;

    #[ORM\ManyToOne(inversedBy: 'prets')]
    private ?User $usert = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMntP(): ?float
    {
        return $this->mntP;
    }

    public function setMntP(float $mntP): self
    {
        $this->mntP = $mntP;

        return $this;
    }

    public function getDatP(): ?\DateTimeInterface
    {
        return $this->datP;
    }

    public function setDatP(\DateTimeInterface $datP): static
    {
        $this->datP = $datP;

        return $this;
    }
    public function getDatEch(): ?\DateTimeInterface
    {
        return $this->datEch;
    }

    public function setDatEch(\DateTimeInterface $datEch): static
    {
        $this->datEch = $datEch;

        return $this;
    }
    public function getusert(): ?user
    {
        return $this->usert;
    }

    public function setusert(?user $usert): self
    {
        $this->usert = $usert;

        return $this;
    }
    public function getPrd(): ?float
    {
        return $this->prd;
    }

    public function setPrd(float $prd): self
    {
        $this->prd = $prd;

        return $this;
    }
    public function getMs(): ?float
    {
        return $this->ms;
    }

    public function setMs(float $ms): self
    {
        $this->ms = $ms;

        return $this;
    }
    public function getStat(): ?string
    {
        return $this->stat;
    }

    public function setStat(string $stat): self
    {
        $this->stat = $stat;

        return $this;
    }

    public function getReliquat(): ?float
    {
        return $this->reliquat;
    }

    public function setReliquat(float $reliquat): self
    {
        $this->reliquat = $reliquat;

        return $this;
    }
    public function getReste(): ?float
    {
        return $this->reste;
    }

    public function setReste(float $reste): self
    {
        $this->reste = $reste;

        return $this;
    }

}