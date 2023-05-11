<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicTest extends WebTestCase
{
    public function testSomething(): void
    {
        //Créer un client
        $client = static::createClient();
        //Qui permet d'aller sur des URL
        $crawler = $client->request('GET', '/');

        //Assert qui confirme que la page existe
        $this->assertResponseIsSuccessful();
    }
}
