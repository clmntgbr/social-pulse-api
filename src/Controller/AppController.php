<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'app_app')]
    public function index(UserRepository $repository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response
    {
        $user = $repository->findOneBy(['email' => 'clement@gmail.com']);
        dd($user);

        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
