<?php

namespace App\Form;

use App\Entity\Ingredient;
//La meme que pour les controlleurs mais sur les form
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//Utilisation du Component Validator\Constraints de Symfony qui permet de gérer des contraintes pour les envoye en bdd
use Symfony\Component\Validator\Constraints as Assert;
//Import des différents type utile au form
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IngredientType extends AbstractType
{
    //Fonction qui crée le formulaire en utilisant FormBuilderInterface de Symfony
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //Definis le type du Champ 
            ->add('name', TextType::class, [
                //Permet de définir les attributs du champ (Comme la class, le nb de caractére du champ) Les attributs sont définis dans un tableau
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                //Utilisation des contraintes comme dans les entités
                'constraints' => [
                    //Contrainte sur le nb de caractéres
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    //Contrainte pour empecher que le champ soit vide
                    new Assert\NotBlank()
                ]
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                //Utilisation des contraintes comme dans les entités
                'constraints' => [
                    //Contrainte pour empecher que le nombre soit négatif
                    new Assert\Positive(),
                    //Contrainte qui définis le max
                    new Assert\LessThan(200)
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4',
                ],
                'label' => 'Créer mon ingrédient'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
