<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\Table("project")
 * @UniqueEntity(
 *      fields={"name"},
 *      errorPath="name",
 *      message="This name is alredy in use"
 *      )
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *  pattern="#^\d+\.\d+\.\d+$#",
     *  message= "The version must follow the pattern x.x.x" 
     * )
     */
    private $version;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="product", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $Name): self
    {
        $this->name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $Description): self
    {
        $this->description = $Description;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $Version): self
    {
        $this->version = $Version;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }
}
