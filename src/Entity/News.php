<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 */
class News
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="news")
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $editedAt;

    /**
     * @ORM\Column(type="boolean")
     */

    private $isDelete;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="news")
     */
    private $category;


    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="news_access_list")
     */

    private $NewsAccessList;

    public function __construct()
    {
        $this->NewsAccessList = new ArrayCollection();
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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }


    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

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

   

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getIsDelete()
    {
        return $this->isDelete;
    }

    public function setIsDelete($isDelete): self
    {
        $this->isDelete = $isDelete;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getNewsAccessList(): Collection
    {
        return $this->NewsAccessList;
    }

    public function addNewsAccessList(User $newsAccessList): self
    {
        if (!$this->NewsAccessList->contains($newsAccessList)) {
            $this->NewsAccessList[] = $newsAccessList;
        }

        return $this;
    }

    public function removeNewsAccessList(User $newsAccessList): self
    {
        if ($this->NewsAccessList->contains($newsAccessList)) {
            $this->NewsAccessList->removeElement($newsAccessList);
        }

        return $this;
    }

    public function getEditedAt(): ?\DateTimeInterface
    {
        return $this->editedAt;
    }

    public function setEditedAt(\DateTimeInterface $editedAt): self
    {
        $this->editedAt = $editedAt;

        return $this;
    }



}
