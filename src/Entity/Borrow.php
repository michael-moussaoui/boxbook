<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BorrowRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: BorrowRepository::class)]
class Borrow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("borrow:read")]
    private ?int $id = null;

    #[ORM\Column (nullable: true)]
    #[Groups("borrow:read")]
    private ?\DateTimeImmutable $borrowAt = null;

    #[ORM\Column (nullable: true)]
    #[Groups("borrow:read")]
    private ?\DateTimeImmutable $borrowReturnAt = null;

    #[ORM\ManyToOne(inversedBy: 'borrows')]
    #[ORM\JoinColumn(name:"book_id", referencedColumnName:"id")]
    private ?Book $book = null;
    
    #[ORM\ManyToOne(inversedBy: 'borrows')]
    #[ORM\JoinColumn(name:"user_id", referencedColumnName:"id")]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorrowAt(): ?\DateTimeImmutable
    {
        return $this->borrowAt;
    }

    public function setBorrowAt(\DateTimeImmutable $borrowAt): self
    {
        $this->borrowAt = $borrowAt;

        return $this;
    }

    public function getBorrowReturnAt(): ?\DateTimeImmutable
    {
        return $this->borrowReturnAt;
    }

    public function setBorrowReturnAt(\DateTimeImmutable $borrowReturnAt): self
    {
        $this->borrowReturnAt = $borrowReturnAt;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

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
}
