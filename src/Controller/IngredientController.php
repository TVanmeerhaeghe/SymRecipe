<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
//Importe le namespace du repository de mes ingrédients
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
//Importe Paginator Interface de Knp
use Knp\Component\Pager\PaginatorInterface;
//La classe Request est utilisée pour récupérer les données envoyées par le client dans une action de contrôleur. Par exemple, 
//elle permet de récupérer les paramètres de l'URL, les paramètres POST envoyés par un formulaire, ou les données JSON envoyées dans le corps de la requête.
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    //Définis la route dans la qu'elle cette fonction seras apeller
    #[Route('/ingredient', name: 'ingredient.index', methods:['GET'])]
    //Fonction pour afficher tous les ingrédients
    //Controller qui renvoie une Response
    //Utilise mon Repository pour les ingrédients et le nome $repository (Injection de dépendances dans les paramétres du controller)
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        //Definis la variable ingrédients qui contient mon répository des ingrédients appeller dans ma fonction
        //Utilise la méthode findAll qui récupére tous les éléments de ce ce repositor
        //Utilise le bundle Paginate qui prend en Param (query, la page de début, le nb d'elements par page)
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        //Methode render() qui  viens de AbstractController et qui renvoie vers un template twig
        //Passe la variable ingrédient a la vue qui contient les éléments de mon repository
        return $this->render('pages/ingredient/index.html.twig', ['ingredients' => $ingredients]);
    }

    #[Route('/ingredient/nouveau', name: 'ingredient.new')]
    //Fonction pour ajouter un igrédient
    public function new(EntityManagerInterface $manager, Request $request) : Response 
    {
        //Crée un nouvel ingrédient
        $ingredient = new Ingredient();
        //Apelle un nouveau formulaire avec la méthode createForm() qui viens d'abstractController
        //En lui passant en param le formulaire (IngrédientType) et le vouvel ingrédient
        $form = $this->createForm(IngredientType::class, $ingredient);

        //Récupére la réquéte en POST qui viens de  la page a la soumissions du formulaire
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            //Si le form est valide définis la variable ingrédient avec les données du formulaire
            $ingredient = $form->getData();

            //Enregistre les données du nouvel ingrédients
            $manager->persist($ingredient);
            //Les envoie en BDD
            $manager->flush();

            //Définis un message qui s'affiche si le formulaire a bien été envoyé
            $this->addFlash(
                'success',
                'Votre ingrédient a été ajouté avec succès !'
            );

            //Redirige aprés la soumission du formulaire vers la route ayant le nom ingredient.index
            return $this->redirectToRoute('ingredient.index');
        }

        //Méthode createView qui est passé au template et qui permet de crée le formulaire pour la View
        return $this->render('pages/ingredient/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/ingredient/edition/{id}', name: 'ingredient.edit', methods:['GET', 'POST'])]
    //Fonction pour éditer un ingrédient
    //Symfony regarde dans l'url le params qu'on passe et vérifie de lui même dans mon entity si un params existe et le récupére de lui même
    public function edit( Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        //Méthode du répository qui permet de trouver un igrédient par son id
        //$ingredient = $repository->findOneBy(["id" => $id]);
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/ingredient/suppression/{id}', name: 'ingredient.delete', methods:['GET'])]
    //Fonction pour supprimer un ingrédient
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient): Response
    {

        //Méthode de l'entity Manager qui permet de supprimer un élément qui correspond au param passer dans l'url
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès !'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
