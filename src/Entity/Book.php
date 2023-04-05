<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("book:read")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("book:read")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups("book:read")]
    private ?string $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("book:read")]
    private ?string $cover = null;

    #[ORM\Column]
    #[Groups("book:read")]
    private ?bool $isAvailable = null;

    #[ORM\Column(length: 255)]
    #[Groups("book:read")]
    private ?string $isbn = null;

    #[ORM\ManyToOne(inversedBy: 'category')]
    #[ORM\JoinColumn (nullable:false)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Borrow::class)]
    private Collection $borrows;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?BoxBook $boxbook = null;

    public function __construct()
    {
        $this->borrows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Borrow>
     */
    public function getBorrows(): Collection
    {
        return $this->borrows;
    }

    public function addBorrow(Borrow $borrow): self
    {
        if (!$this->borrows->contains($borrow)) {
            $this->borrows->add($borrow);
            $borrow->setBook($this);
        }

        return $this;
    }

    public function removeBorrow(Borrow $borrow): self
    {
        if ($this->borrows->removeElement($borrow)) {
            // set the owning side to null (unless already changed)
            if ($borrow->getBook() === $this) {
                $borrow->setBook(null);
            }
        }

        return $this;
    }

    public function getBoxbook(): ?BoxBook
    {
        return $this->boxbook;
    }

    public function setBoxbook(?BoxBook $boxbook): self
    {
        $this->boxbook = $boxbook;

        return $this;
    }
}
