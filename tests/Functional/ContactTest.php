<?php

namespace App\Tests\Functionnal;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    public function testContactForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formulaire de contact');

        //Récupére le formulaire
        $submitButton = $crawler->selectButton('Envoyer mon message');
        $form = $submitButton->form();

        $form["contact[fullName]"] = "Jean Dupont";
        $form["contact[email]"] = "jd@symrecipe.com";
        $form["contact[subject]"] = "Test";
        $form["contact[message]"] = "Test";

        //Soumet le formulaire
        $client->submit($form);

        //Verifire le statut HTTP 
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Verifie que l'mail a bien étais envoyé
        // $this->assertEmailCount(1);

        $client->followRedirect();

        //Verifie la présence du message de succés
        $this->assertSelectorTextContains('div.alert.alert-success.mt-4', 'Votre message à été envoyé avec succès !');

    }
}
