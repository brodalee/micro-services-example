<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate;

    #[ORM\Column]
    private string $ram;

    #[ORM\Column]
    private string $storage;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $resolution = null;

    #[ORM\Column(length: 255)]
    private ?string $processor = null;

    #[ORM\Column]
    private string $frontCameraResolution;

    #[ORM\Column]
    private string $backCameraResolution;

    #[ORM\Column(length: 255)]
    private ?string $imgUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->creationDate = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getRam(): string
    {
        return $this->ram;
    }

    public function setRam(string $ram): static
    {
        $this->ram = $ram;

        return $this;
    }

    public function getStorage(): string
    {
        return $this->storage;
    }

    public function setStorage(string $storage): static
    {
        $this->storage = $storage;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function setResolution(string $resolution): static
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getProcessor(): ?string
    {
        return $this->processor;
    }

    public function setProcessor(string $processor): static
    {
        $this->processor = $processor;

        return $this;
    }

    public function getFrontCameraResolution(): string
    {
        return $this->frontCameraResolution;
    }

    public function setFrontCameraResolution(string $frontCameraResolution): static
    {
        $this->frontCameraResolution = $frontCameraResolution;

        return $this;
    }

    public function getBackCameraResolution(): string
    {
        return $this->backCameraResolution;
    }

    public function setBackCameraResolution(string $backCameraResolution): static
    {
        $this->backCameraResolution = $backCameraResolution;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }
}
