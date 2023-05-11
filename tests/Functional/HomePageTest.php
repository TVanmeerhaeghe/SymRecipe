<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    //Function qu itest la présence différents élements dans la homePage
    public function testSomething(): void
    {
        //Crée un client web
        $client = static::createClient();
        //Fais une requéte
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $button = $crawler->filter('.btn.btn-primary.btn-lgz');
        $this->assertEquals(1, count($button));

        $recipes = $crawler->filter('.recipes .card');
        $this->assertEquals(3, count($recipes));

        $this->assertSelectorTextContains('h1', 'Bienvenue sur SymRecipe');
    }
}
