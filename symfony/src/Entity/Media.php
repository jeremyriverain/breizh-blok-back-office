<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Interfaces\IContainsMedia;
use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/media/{id}'),
        new GetCollection(uriTemplate: '/media'),
    ]
)]
class Media implements IContainsMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: "media", fileNameProperty: "filePath", dimensions: 'imageDimensions')]
    private File|null $file = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["read"])]
    private ?string $filePath = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $updatedAt;

    #[ORM\ManyToOne(targetEntity: Rock::class, inversedBy: "pictures")]
    private ?Rock $rock;

    /**
     * @var Collection<int, LineBoulder>|LineBoulder[]
     */
    #[ORM\OneToMany(targetEntity: LineBoulder::class, mappedBy: "rockImage", orphanRemoval: true)]
    private Collection $lineBoulders;

    #[Groups(["read"])]
    public ?string $filterUrl;

    #[Groups(["read"])]
    public ?string $contentUrl;

    /**
     * @var array<int, int>
     */
    #[ORM\Column(type: 'simple_array', nullable: true)]
    #[Groups(["read"])]
    private ?array $imageDimensions = [];

    public function __construct()
    {
        $this->lineBoulders = new ArrayCollection();
    }

    /**
     * @return array<int, string>
     */
    public function getMediaAttributes(): array
    {
        return [
            'media'
        ];
    }

    public function getMedia(): self
    {
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
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

    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getRock(): ?Rock
    {
        return $this->rock;
    }

    public function setRock(?Rock $rock): self
    {
        $this->rock = $rock;

        return $this;
    }

    /**
     * @return Collection<int, LineBoulder>|LineBoulder[]
     */
    public function getLineBoulders(): Collection
    {
        return $this->lineBoulders;
    }

    public function addLineBoulder(LineBoulder $lineBoulder): self
    {
        if (!$this->lineBoulders->contains($lineBoulder)) {
            $this->lineBoulders[] = $lineBoulder;
            $lineBoulder->setRockImage($this);
        }

        return $this;
    }

    public function removeLineBoulder(LineBoulder $lineBoulder): self
    {
        if ($this->lineBoulders->removeElement($lineBoulder)) {
            // set the owning side to null (unless already changed)
            if ($lineBoulder->getRockImage() === $this) {
                $lineBoulder->setRockImage(null);
            }
        }

        return $this;
    }

    /**
     * @return array<int, int>
     */
    public function getImageDimensions(): ?array
    {
        return $this->imageDimensions ?? [];
    }

    /**
     * @param array<int, int> $imageDimensions
     */
    public function setImageDimensions(?array $imageDimensions = []): self
    {
        $this->imageDimensions = $imageDimensions ?? [];

        return $this;
    }
}
