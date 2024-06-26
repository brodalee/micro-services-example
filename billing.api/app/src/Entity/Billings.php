<?php

namespace App\Entity;

use App\Repository\BillingsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: BillingsRepository::class)]
class Billings
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate;

    #[ORM\Column(length: 255)]
    private ?string $userId = null;

    #[ORM\Column]
    private ?float $tva = null;

    /**
     * @var Collection<int, BillingsItems>
     */
    #[ORM\OneToMany(targetEntity: BillingsItems::class, mappedBy: 'billing', cascade: ['PERSIST'])]
    private Collection $items;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentReference = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->creationDate = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(float $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * @return Collection<int, BillingsItems>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(BillingsItems $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setBilling($this);
        }

        return $this;
    }

    public function removeItem(BillingsItems $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getBilling() === $this) {
                $item->setBilling(null);
            }
        }

        return $this;
    }

    public function getPaymentReference(): ?string
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(?string $paymentReference): static
    {
        $this->paymentReference = $paymentReference;

        return $this;
    }

    public function getTotalPrice(): int
    {
        $totalPrice = 0;
        foreach ($this->items->toArray() as $item) {
            $totalPrice += $item->getPrice() * $item->getQuantity();
        }

        return $totalPrice;
    }
}
