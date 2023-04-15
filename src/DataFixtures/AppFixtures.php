<?php

namespace App\DataFixtures;

//Utilise les namespace nécésssaire a Faker
use Faker\Factory;
use Faker\Generator;
//Utilise le namespace pour récupérer mes entité
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
//En utilisant la classe ObjectManager, on peut interagir avec la base de données en utilisant l'API d'abstraction de la couche d'accès aux données 
//fournie par Doctrine. Cette API fournit des méthodes pour effectuer des opérations courantes sur la base de données, 
//telles que l'ajout, la mise à jour et la suppression d'objets, ainsi que des requêtes pour récupérer des données de la base de données.
use Doctrine\Persistence\ObjectManager;
//Utilise les fixtures
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    //Récupére Faker
    private Generator $faker;


    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {   
        //Boucle pour crée 10 utilisateurs
        $users = [];
        for ($i=0; $i < 10; $i++) { 
            $user = new User();
            $user->setFullName($this->faker->name())
            ->setPseudo(mt_rand(0,1) === 1 ? $this->faker->firstName() : null)
            ->setEmail($this->faker->email())
            ->setRoles(['ROLE_USER'])
            //Hash le mdp en bdd grace au EntityListener
            //EN allant sur l'entity User il va voir qu'il y a un tag qui renvoie vers Un ENtityListener et qui va exécuter les fonctions dedans avant de persist
            ->setPlainPassword('password');

            $users[] = $user;
            $manager->persist($user);
        }

        //Boucle pour crée 50 ingrédients
        $ingredients = [];
        for ($i=0; $i < 50; $i++) { 
            //Crée un nouvel ingrédient
            $ingredient = new Ingredient();
            //set ses différentes column avec les données choisies
            //Utilise les méthodes de faker pour générer des fausses données plus réelles
            $ingredient->setName($this->faker->word())
            ->setPrice(mt_rand(1, 100))
            //Assigne un utilisateur de notre tableau $users a l'ingrédient de maniére aléatoire en partant de zéro et e allant jusqu'au dernier user du tableau
            ->setUser($users[mt_rand(0, count($users) - 1)]);

            $ingredients[] = $ingredient;
            //persist signale a symfony que les données doivent être enregistré (ajoute l'objet modifié a la liste des objets a envoyer lors du prochain flush)
            $manager->persist($ingredient);
        }

        //Boucle pour crée 25 recettes
        for ($j=0; $j < 25; $j++) { 

            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
            ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
            ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
            ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
            ->setDescription($this->faker->text(300))
            ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
            ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
            ->setUser($users[mt_rand(0, count($users) - 1)]);

            for ($k=0; $k < mt_rand(5, 15) ; $k++) { 
                // Ajoute un ingrédient qui viens des fixtures pour les ingrédients au hasard
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }

            $manager->persist($recipe);
        }

        //flush signale a symfony que les données doivent être envoyer en bdd
        $manager->flush();
    }
}
