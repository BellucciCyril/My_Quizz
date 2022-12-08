<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    // #[Route('/', name: 'main')]
    // public function index(): Response
    // {
    //     return $this->render('main/index.html.twig');
    // }

    #[Route('/', name: 'main')]
    public function index(EntityManagerInterface $manager): Response
    {
        $categories = $manager->getRepository(Categorie::class)->findAll();
        return $this->render('main/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
