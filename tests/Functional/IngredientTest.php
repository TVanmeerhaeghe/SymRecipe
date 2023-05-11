<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Ingredient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    // //Function qui test si la création d'ingrédient est fonctionnel
    // public function testIfCreateIngredientIsSuccessfull(): void
    // {
    //     $client = static::createClient();

    //     $urlGenerator = $client->getContainer()->get('router');

    //     $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

    //     $user = $entityManager->find(User::class, 1);

    //     $client->loginUser($user);

    //     $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.new'));

    //     $form = $crawler->filter('form[name=ingredient]')->form([
    //         'ingredient[name]' => "Un ingrédient",
    //         'ingredient[price]' => floatval(33)
    //     ]);

    //     $client->submit($form);

    //     $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

    //     $client->followRedirect();

    //     $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été créé avec succés !');

    //     $this->assertRouteSame('ingredient.index');

    // }

    //Function qu itest si l'affichage des ingrédients est fonctionnel
    public function testIfListingIngredientIsSuccessfull(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get("router");

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.index'));

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('ingredient.index');

    }

    //Function qu itest la modification d'un ingrédient
    public function testIfUpdateAnIngredientIsSuccessfull(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        //Retrouve un ingrédient de l'user récupéré avant
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            //Récupére l'id de l'ingrédient pour le mettre dans l'url
            $urlGenerator->generate('ingredient.edit', ['id' => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "Un ingrédient 2",
            'ingredient[price]' => floatval(34)
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été modifié avec succès !');

        $this->assertRouteSame('ingredient.index');
    }

    //Function qui test la supression d'un ingredient
    public function testIfDeleteAnIngredientIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.delete', ['id' => $ingredient->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été supprimé avec succès !');

        $this->assertRouteSame('ingredient.index');
    }


}
