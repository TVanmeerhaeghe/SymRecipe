<?php

namespace App\Controller;


use App\Entity\Recipe;
use App\Entity\Mark;
use App\Form\RecipeType;
use App\Form\MarkType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'recipe.index', methods:['GET'])]
    //Fonction qui apelle toutes les recettes
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user'=>$this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', ['recipes' => $recipes]);
    }

    #[Route('/recette/communaute', name: 'recipe.community', methods:['GET'])]
    //Fonction qu affiche toutes les recette qui sont public
    public function indexPublic( PaginatorInterface $paginator, RecipeRepository $repository, Request $request): Response
    {   
        $recipes = $paginator->paginate(
            //Apelle la fonction findPublicRecipe du repository Recipe qui permet de trouver les recette public avec du DQL
            $repository->findPublicRecipe(null),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/community.html.twig', ['recipes' => $recipes]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recette/creation', name: 'recipe.new', methods:['GET', 'POST'])]
    //Fonction pour ajouter une recette
    public function new(EntityManagerInterface $manager, Request $request) : Response 
    {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été ajouté avec succès !'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', ['form' => $form->createView()]);
    }

    //Vérifie que l'user est connecté et que la recette a la qu'elle il souhaite accéder est publique
    #[Security("is_granted('ROLE_USER') and recipe.getIsPublic() == true")]
    #[Route('/recette/{id}', name: 'recipe.show', methods:['GET', 'POST'])]
    //Function qui affiche une recette en particulier si elle est publique et pour noter cette même recette
    public function show(Recipe $recipe, Request $request, MarkRepository $markRepository, EntityManagerInterface $manager): Response
    {
        $mark = New Mark;
        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $mark->setUser($this->getUser())
            ->setRecipe($recipe);

            //Permet de récupérer une entité Mark existante si l'utilisateur actuel a déjà noté la recette spécifiée
            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(),
                'recipe' => $recipe
            ]);

            //Si l'utilsiateur ne l'a pas notée persist la note
            if(!$existingMark){
                $manager->persist($mark);
            //Sinon change la note qui est présente en bdd
            } else {
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre note a bien été prise en compte !'
            );

            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }

        return $this->render('pages/recipe/show.html.twig', ['recipe' => $recipe, 'form' => $form->createView()]);
    }

    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recette/edition/{id}', name: 'recipe.edit', methods:['GET', 'POST'])]
    //Fonction pour éditer une recette
    public function edit( Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès !'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/recette/suppression/{id}', name: 'recipe.delete', methods:['GET'])]
    //Fonction pour supprimer une recette
    public function delete(EntityManagerInterface $manager, Recipe $recipe): Response
    {

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succès !'
        );

        return $this->redirectToRoute('recipe.index');
    }
}
