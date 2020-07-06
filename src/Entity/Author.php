<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
 
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert; 

/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 * @Vich\Uploadable
 */
class Author implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="author", orphanRemoval=true)
     */
    private $annonces;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @Vich\UploadableField(mapping="user_pictures", fileNameProperty="picture")
     * @Assert\File(
     * maxSize="2M",
     * mimeTypes= {"image/png", "image/jpeg"},
     * mimeTypesMessage = "Seules les images jpg et png sont acceptées"
     * )
     */
    private $pictureFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateOn;

    public function __construct()
    {
        $this->Author = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->username;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        $this->updatedOn = new \DateTime();
        return $this;
  
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        $this->updatedOn = new \DateTime();
        return $this;

 
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        $this->updatedOn = new \DateTime();
        return $this;

         
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         //$this->plainPassword = null;
    }
    public function createdOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }
    public function setCreatedOn(\DateTimeInterface $createdOn):self
{
    $this->createdOn = $createdOn;

    return $this;
}


    /**
     * @return Collection|Annonce[]
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->Annonce[] = $annonce;
            $annonce->setAuthor($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->contains($annonce)) {
            $this->annonces->removeElement($annonce);
            // set the owning side to null (unless already changed)
            if ($annonce->getAuthor() === $this) {
                $annonce->setAuthor(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }
    public function setPictureFile(?File $pictureFile=null):void
    {
        $this->pictureFile = $pictureFile;
    
    if ($pictureFile !== null){
        $this->updateOn = new \DateTime();
        
    }
}

    public function getUpdateOn(): ?\DateTimeInterface
    {
        return $this->updateOn;
    }

    public function setUpdateOn(?\DateTimeInterface $updateOn): self
    {
        $this->updateOn = $updateOn;

        return $this;
    }
}