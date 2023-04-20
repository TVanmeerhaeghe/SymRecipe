<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            //Definis le nom de l'entity quandi l y en a plusieurs
            ->setEntityLabelInPlural('Demande de contacts')
            //Pareil mais au singulier
            ->setEntityLabelInSingular('Demande de contact')
            ->setPageTitle("index", "Symrecipe - Administration des demandes de contact")
            ->setPaginatorPageSize(20)
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');           
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('fullName'),
            TextField::new('email'),
            TextField::new('subject'),
            DateTimeField::new('createdAt')->setFormTypeOption('disabled', 'disabled'),
            TextareaField::new('message')
                ->hideOnIndex()
                ->setFormType(CKEditorType::class),
        ];
    }
    
}
