<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use SYmfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{   
    //Définis la route dans la qu'elle cette fonction seras apeller ainsi que sa méthode pour éviter tous problémes 
    #[Route('/', 'home.index', methods: ['GET'])]
    //Controller qui renvoie une Response
    public function index(): Response
    {   
        //Methode render() qui  viens de AbstractController et qui renvoie vers un template twig
        return $this->render('home.html.twig');
    }
};