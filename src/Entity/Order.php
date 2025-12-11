<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $customer_name = null;

    #[ORM\Column(length: 255)]
    private ?string $customer_email = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $quantity = 1;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $special_request = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $order_date = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'Pending';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total_price = null;

    public function __construct()
    {
        $this->order_date = new \DateTime();
        $this->status = 'Pending';
    }

    public function getId(): ?int { return $this->id; }

    public function getCustomerName(): ?string { return $this->customer_name; }
    public function setCustomerName(string $customer_name): static { $this->customer_name = $customer_name; return $this; }

    public function getCustomerEmail(): ?string { return $this->customer_email; }
    public function setCustomerEmail(string $customer_email): static { $this->customer_email = $customer_email; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(string $phone): static { $this->phone = $phone; return $this; }

    public function getMenu(): ?Menu { return $this->menu; }
    public function setMenu(?Menu $menu): static { $this->menu = $menu; return $this; }

    public function getQuantity(): ?int { return $this->quantity; }
    public function setQuantity(int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getSpecialRequest(): ?string { return $this->special_request; }
    public function setSpecialRequest(?string $special_request): static { $this->special_request = $special_request; return $this; }

    public function getOrderDate(): ?\DateTimeInterface { return $this->order_date; }
    public function setOrderDate(\DateTimeInterface $order_date): static { $this->order_date = $order_date; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getTotalPrice(): ?string { return $this->total_price; }
    public function setTotalPrice(string $total_price): static { $this->total_price = $total_price; return $this; }
}
