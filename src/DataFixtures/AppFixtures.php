<?php

namespace App\DataFixtures;

//Utilise les namespace nécésssaire a Faker
use Faker\Factory;
use Faker\Generator;
//Utilise le namespace pour récupérer mon ingrédient
use App\Entity\Ingredient;
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
        for ($i=0; $i < 50; $i++) { 
            //Crée un nouvel ingrédient
            $ingredient = new Ingredient();
            //set ses différentes column avec les données choisies
            //Utilise les méthodes de faker pour générer des fausses données plus réelles
            $ingredient->setName($this->faker->word())
            ->setPrice(mt_rand(1, 100));

            //persist signale a symfony que les données doivent être enregistré (ajoute l'objet modifié a la liste des objets a envoyer lors du prochain flush)
            $manager->persist($ingredient);
        }

        //flush signale a symfony que les données doivent être envoyer en bdd
        $manager->flush();
    }
}
