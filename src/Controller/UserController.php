<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods:['GET', 'POST'])]
    //Function qui permet l'édition du profil d'un utilisateur
    public function edit(User $user, EntityManagerInterface $manager, Request $request, UserPasswordHasherInterface $hasher): Response
    {

        //Vérifier que l'user est connecté
        if(!$this->getUser()){
            return $this->redirectToRoute('security.login');
        }

        //Vérifie que l'user correspond bien a l'id de l'user dans l'url
        if($this->getUser() !== $user){
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Vérifie que le mot dep asse rentré par l'utilsiateur correspond 
            if($hasher->isPasswordValid($user, $form->getData()->getPlainPassword()))
            {
                $user = $form->getData();

                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Votre profil à bien été modifié !'
                );
    
                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }

        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user.edit.password', methods:['GET', 'POST'])]
    //Function qui permet l'édition du mot de passe  d'un utilisateur
    public function editPassword(User $user, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($user, $form->getData()['plainPassword']))
            {
                $user->setPassword(
                    $hasher->hashPassword(
                        $user,
                        $form->getData()['newPassword']
                    )
                );

                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Votre mot de passe à été modifié avec succés'
                );
    
                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }
        }
        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView(),
        ]); 
    }

}