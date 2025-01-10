<?php

namespace App\Controller\Admin;

use App\Entity\Organization;
use App\Entity\Publication\Publication;
use App\Entity\SocialNetwork\SocialNetwork;
use App\Entity\SocialNetwork\Type;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(PublicationCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Publication', 'fas fa-list', Publication::class);
        yield MenuItem::linkToCrud('Organization', 'fas fa-list', Organization::class);
        yield MenuItem::linkToCrud('SocialNetwork', 'fas fa-list', SocialNetwork::class);
        yield MenuItem::linkToCrud('SocialNetworkType', 'fas fa-list', Type::class);
        yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
    }
}