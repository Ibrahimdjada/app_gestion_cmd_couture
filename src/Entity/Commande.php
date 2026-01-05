<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $typeCom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datCom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datRec = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(type:'float', nullable:true)]
    private ?float $avance = null;

    #[ORM\Column(type:'float', nullable:true)]
    private ?float $reste = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $user = null;

    #[ORM\Column(type:'float', nullable:true)]
    private ?float $reliquat = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn( nullable: true)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn( nullable: true)]
    private ?User $updatedBy = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $usert = null;

    
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $pathMod = [];

    /**
     * @var UploadedFille[]|null
     * @ORM\Transient()
     */
    private ?array $filemod = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $pathTissu = [];
    
    /**
     * @var UploadedFille[]|null
     * @ORM\Transient()
     */
    private ?array $filetissu = null;

    /**
     * @var Collection<int, Rdv>
     */
    #[ORM\OneToMany(targetEntity: Rdv::class, mappedBy: 'commande')]
    private Collection $rdvs;

    public function __construct()
    {
        $this->rdvs = new ArrayCollection();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeCom(): ?string
    {
        return $this->typeCom;
    }

    public function setTypeCom(string $typeCom): self
    {
        $this->typeCom = $typeCom;

        return $this;
    }

    public function getDatCom(): ?\DateTimeInterface
    {
        return $this->datCom;
    }

    public function setDatCom(\DateTimeInterface $datCom): self
    {
        $this->datCom = $datCom;

        return $this;
    }

    public function getDatRec(): ?\DateTimeInterface
    {
        return $this->datRec;
    }

    public function setDatRec(\DateTimeInterface $datRec): self
    {
        $this->datRec = $datRec;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getAvance(): ?float
    {
        return $this->avance;
    }

    public function setAvance(float $avance): self
    {
        $this->avance = $avance;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
    
    public function getUsert(): ?User
    {
        return $this->usert;
    }

    public function setUsert(?User $usert): self
    {
        $this->usert = $usert;

        return $this;
    }

    /**
     * @return Collection<int, Rdv>
     */
    public function getRdvs(): Collection
    {
        return $this->rdvs;
    }

    public function addRdv(Rdv $rdv): static
    {
        if (!$this->rdvs->contains($rdv)) {
            $this->rdvs->add($rdv);
            $rdv->setCommande($this);
        }

        return $this;
    }

    public function removeRdv(Rdv $rdv): static
    {
        if ($this->rdvs->removeElement($rdv)) {
            // set the owning side to null (unless already changed)
            if ($rdv->getCommande() === $this) {
                $rdv->setCommande(null);
            }
        }

        return $this;
    }

    public function getPathMod(): array
    {
        return $this->pathMod ?? [];
    }
    public function setPathMod(array $pathMod): self
    {
        $this->pathMod=$pathMod;
        return $this;
    }

    public function addPathMod(string $path): self
    {
        $this->pathMod[] = $path;

        return $this;
    }
    public function getFilemod(): array
    {
        return $this->filemod;
    }
    public function setFilemod(array $filemod): self
    {
        $this->filemod=$filemod;
        return $this;
    }
    public function uploadMod(): void
    {
        if (null=== $this->getFilemod()){
            return;
        }
        foreach ($this->getFilemod() as $file) {
            if ($file instanceof UploadedFile){
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getUploadRootDir(), $fileName);
               
                $this->addPathMod($this->getUploadDir() . '/' . $fileName);
            }
        }
        $this->filemod=null;
    }
    public function removePathMod(string $path): self
    {
        if (($key = array_search($path, $this->pathMod)) !== false) {
            unset($this->pathMod[$key]);
        }

        return $this;
    }

    public function getPathTissu(): array
    {
        return $this->pathTissu ?? [];
    }
    public function setPathTissu(array $pathTissu): self
    {
        $this->pathTissu=$pathTissu;
        return $this;
    }


    public function addPathTissu(string $path): self
    {
        $this->pathTissu[] = $path;

        return $this;
    }


    public function getFiletissu(): array
    {
        return $this->filetissu;
    }
    public function setFiletissu(array $filetissu): self
    {
        $this->filetissu=$filetissu;
        return $this;
    }

    public function uploadTissu(): void
    {
        if (null=== $this->getFiletissu()){
            return;
        }
        foreach ($this->getFiletissu() as $file) {
            if ($file instanceof UploadedFile){
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getUploadRootDir(), $fileName);
               
                $this->addPathTissu($this->getUploadDir() . '/' . $fileName);
            }
        }
        $this->filetissu=null;
    }
    protected function getUploadRootDir(): string
    {
        return __DIR__ . '/../../public/' . $this->getUploadDir();
    }

    protected function getUploadDir(): string
    {
        return 'uploads/Images';
    }
    public function removePathTissu(string $path): self
    {
        if (($key = array_search($path, $this->pathTissu)) !== false) {
            unset($this->pathTissu[$key]);
        }

        return $this;
    }
}
