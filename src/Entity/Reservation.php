<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Menu;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 11)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?int $guest = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\ManyToMany(targetEntity: Menu::class)]
    #[ORM\JoinTable(name: "reservation_menu")]
    private Collection $menus;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Menu $menu = null;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(string $phone): static { $this->phone = $phone; return $this; }
    public function getGuest(): ?int { return $this->guest; }
    public function setGuest(int $guest): static { $this->guest = $guest; return $this; }
    public function getDate(): ?\DateTime { return $this->date; }
    public function setDate(\DateTime $date): static { $this->date = $date; return $this; }
    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $message): static { $this->message = $message; return $this; }

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): static
    {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
        }
        return $this;
    }

    public function removeMenu(Menu $menu): static
    {
        $this->menus->removeElement($menu);
        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;

        return $this;
    }
}
