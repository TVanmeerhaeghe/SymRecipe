<?php

namespace App\Tests\App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\User;
use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
    //Crée un Recipe pour la distribué aux autres function
    public function getEntity() : Recipe
    {
        return (new Recipe())->setName('Recipe 1')
            ->setDescription('Description 1')
            ->setIsFavorite(true)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
    }

    //Test la validité des informations envoyé a l'entité (Test via les assert)
    public function testEntityIsValide(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        //Recupére la recette crée par la function getEntity
        $recipe = $this->getEntity();
        // Vérifié la validité des informations envoyé
        $errors = $container->get('validator')->validate($recipe);

        //Le nombre donne le nombre d'erreur attendu 
        $this->assertCount(0, $errors);

    }

    //Function pour vérifier la validité du nom de la recipe
    public function testInvalidName(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $recipe = $this->getEntity();
        $recipe->setName('');

        $errors = $container->get('validator')->validate($recipe);

        //Deux erreurs attendu car deux asserts ne sont pas remplis sur ce champ
        $this->assertCount(2, $errors);
    }

    //Function qui test l'average des notes d'une recette
    public function testGetAverage(): void
    {
        self::bootKernel();
        
        $recipe = $this->getEntity();
        //Apelle  Doctrine pour qu'il récupére le premier utilisateur de la bdd
        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1);

        for ($i=0; $i < 5; $i++) { 
            $mark = new Mark;
            $mark->setMark(2)
                ->setUser($user)
                ->setRecipe($recipe);

            $recipe->addMark($mark);
        }

        //Test que l'average de la recipe soit strictement égale a 2.0 (float)
        $this->assertTrue(2.0 === $recipe->getAverage());
    }
}
