<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    //Permet de renseigner sur quelle entité on souhaite agir
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    //Permet de configurer le front de la page CRUD de cette entité
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            //Definis le nom de l'entity quandi l y en a plusieurs
            ->setEntityLabelInPlural('Utilisateurs')
            //Pareil mais au singulier
            ->setEntityLabelInSingular('Utilisateur')
            ->setPageTitle("index", "Symrecipe - Administration des utilisateurs")
            ->setPaginatorPageSize(10);           
    }

    //Affiche les champs choisit les champs qui sont afficher
    public function configureFields(string $pageName): iterable
    {
        return [
            //hideOnForm permet de cacher le champ dans l'édition
            IdField::new('id')->hideOnForm(),
            TextField::new('fullName'),
            TextField::new('pseudo'),
            //setFormTypeOption affiche le champ mais pas possible de le modifier
            TextField::new('email')->setFormTypeOption('disabled', 'disabled)'),
            //hideOnIndex cache dans la liste mais pas dans la modification
            ArrayField::new('roles')->hideOnIndex(),
            DateTimeField::new('createdAt')->setFormTypeOption('disabled', 'disabled'),
        ];
    }
    
}
