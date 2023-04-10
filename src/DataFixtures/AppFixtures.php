<?php

namespace App\DataFixtures;

//Utilise les namespace nécésssaire a Faker
use Faker\Factory;
use Faker\Generator;
//Utilise le namespace pour récupérer mon ingrédient
use App\Entity\Ingredient;
use App\Entity\Recipe;
//En utilisant la classe ObjectManager, on peut interagir avec la base de données en utilisant l'API d'abstraction de la couche d'accès aux données 
//fournie par Doctrine. Cette API fournit des méthodes pour effectuer des opérations courantes sur la base de données, 
//telles que l'ajout, la mise à jour et la suppression d'objets, ainsi que des requêtes pour récupérer des données de la base de données.
use Doctrine\Persistence\ObjectManager;
//Utilise les fixtures
use Doctrine\Bundle\FixturesBundle\Fixture;

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
        //Boucle pour crée 50 ingrédients
        $ingredients = [];
        for ($i=0; $i < 50; $i++) { 
            //Crée un nouvel ingrédient
            $ingredient = new Ingredient();
            //set ses différentes column avec les données choisies
            //Utilise les méthodes de faker pour générer des fausses données plus réelles
            $ingredient->setName($this->faker->word())
            ->setPrice(mt_rand(1, 100));

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
            ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);

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
