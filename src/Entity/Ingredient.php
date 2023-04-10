<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\IngredientRepository;
//Utilisation du Component Validator\Constraints de Symfony qui permet de gérer des contraintes pour les envoye en bdd
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

//Tag ORM, Cette Class est tag comme un entité et est relié a la class Ingredient Repository du dossier Repository
#[ORM\Entity(repositoryClass: IngredientRepository::class)]
//Tag UniqueEntity qui permet d'empécher qu'une données ai deux fois le même nom
#[UniqueEntity('name')]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    //Récupére les contraintes de Symfony par le nom Assert
    //Utilise la contrainte NotNull pour empécher que le champ soit vide
    #[Assert\NotBlank()]
    //utilise la contrainte Length qui limite le nombre de caractéres
    #[Assert\Length(min:2,max:50)]
    private ?string $name = null;

    #[ORM\Column]
    //Utilise la contrainte NotNull pour empécher que le champ soit vide
    #[Assert\NotNull()]
    //Utilise la contrainte Positive pour obliger le nombre rentré a être positif
    #[Assert\Positive()]
    //Utilise la contrainte LessThan pour obliger le nombre a être au maximum 200
    #[Assert\LessThan(200)]
    private ?float $price = null;

    #[ORM\Column]
    //Utilise la contrainte NotNull pour empécher que le champ soit vide
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt = null;

    //Fonction construct pour l'entité Ingrédient
    public function __construct()
    {
        //A chaque contruction de l'objet on definis le createdAt en tant que DateTime
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    //Permet de renvoyer le nom de l'ingrédient a la place de son id dnas les recettes
    public function __toString()
    {
        return $this->name;
    }
}
