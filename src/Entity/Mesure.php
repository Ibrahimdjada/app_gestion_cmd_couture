<?php

namespace App\Entity;

use App\Repository\MesureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MesureRepository::class)]
class Mesure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
    private ?float $epaule = null;

    #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
    
    private ?float $poitrine = null;

    #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
    private ?float $manche = null;

    #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
    private ?float $encolure = null;

    #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
    private ?float $poignee = null;

    #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
    private ?float $ecartDos = null;

    #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
    private ?float $tourVentrale = null;

    #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
    private ?float $longueur = null;

    #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
     private ?float $cuisse = null;

     #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
     private ?float $fermeture = null;

     #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
     private ?float $ceinture = null;

     #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
     private ?float $taille = null;

     #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
    private ?float $longueurPantalon = null;

     #[ORM\Column(type:'decimal', precision:4, scale:2, nullable:true)]
     private ?float $basPantalon = null;

    #[ORM\ManyToOne(inversedBy: 'mesures')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEpaule(): ?float
    {
        return $this->epaule;
    }

    public function setEpaule(float $epaule): static
    {
        $this->epaule = $epaule;

        return $this;
    }

    public function getPoitrine(): ?float
    {
        return $this->poitrine;
    }

    public function setPoitrine(float $poitrine): static
    {
        $this->poitrine = $poitrine;

        return $this;
    }

    public function getManche(): ?float
    {
        return $this->manche;
    }

    public function setManche(float $manche): static
    {
        $this->manche = $manche;

        return $this;
    }

    public function getEncolure(): ?float
    {
        return $this->encolure;
    }

    public function setEncolure(float $encolure): self
    {
        $this->encolure = $encolure;

        return $this;
    }

    public function getPoignee(): ?float
    {
        return $this->poignee;
    }

    public function setPoignee(float $poignee): self
    {
        $this->poignee = $poignee;

        return $this;
    }

    public function getEcartDos(): ?float
    {
        return $this->ecartDos;
    }

    public function setEcartDos(float $ecartDos): self
    {
        $this->ecartDos = $ecartDos;

        return $this;
    }

    public function getTourVentrale(): ?float
    {
        return $this->tourVentrale;
    }

    public function setTourVentrale(float $tourVentrale): self
    {
        $this->tourVentrale = $tourVentrale;

        return $this;
    }

    public function getLongueur(): ?float
    {
        return $this->longueur;
    }

    public function setLongueur(float $longueur): self
    {
        $this->longueur = $longueur;

        return $this;
    }

    public function getCuisse(): ?float
    {
        return $this->cuisse;
    }

    public function setCuisse(float $cuisse): self
    {
        $this->cuisse = $cuisse;

        return $this;
    }

    public function getFermeture(): ?float
    {
        return $this->fermeture;
    }

    public function setFermeture(float $fermeture): self
    {
        $this->fermeture = $fermeture;

        return $this;
    }

    public function getCeinture(): ?float
    {
        return $this->ceinture;
    }

    public function setCeinture(float $ceinture): self
    {
        $this->ceinture = $ceinture;

        return $this;
    }

    public function getTaille(): ?float
    {
        return $this->taille;
    }

    public function setTaille(float $taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    public function getLongueurPantalon(): ?float
    {
        return $this->longueurPantalon;
    }

    public function setLongueurPantalon(float $longueurPantalon): self
    {
        $this->longueurPantalon = $longueurPantalon;

        return $this;
    }

    public function getBasPantalon(): ?float
    {
        return $this->basPantalon;
    }

    public function setBasPantalon(float $basPantalon): self
    {
        $this->basPantalon = $basPantalon;

        return $this;
    }

 
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

}
