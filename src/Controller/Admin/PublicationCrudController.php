<?php

namespace App\Controller\Admin;

use App\Entity\Publication\Publication;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PublicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Publication::class;
    }
}
