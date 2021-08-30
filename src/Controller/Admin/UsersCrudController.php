<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UsersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Users::class;
    }

    //supprimer des utilisateurs sans vraiment les supprimer
    
    public function configureActions(Actions $actions): Actions
    {
        $detailUsers = Action::new('detailUsers', 'Details', 'fa fa-user')
        ->linkToCrudAction('detail')
        ->addCssClass('btn btn-info');

        return $actions
        ->setPermission('DELETE' , 'ROLE_SUPER_ADMIN')
        ->Add(Crud::PAGE_INDEX, $detailUsers);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('nom'),
            TextField::new('prenom'),
            TextField::new('email'),
            AssociationField::new('restaurants'),
            //TextEditorField::new('description'),
        ];
    }
    
    
}
