<?php

namespace App\Controller\Admin;

use App\Entity\BoxBook;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BoxBookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BoxBook::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
