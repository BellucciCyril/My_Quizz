<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\User;
use App\Form\AddQuestionsType;
use App\Form\AddQuizzType;
use App\Form\AddReponsesType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddQuizzController extends AbstractController
{
    #[Route('/add/quizz/{id_question}/{id_categorie}', name: 'addReponse')]
    public function addReponse(Request $request, EntityManagerInterface $entityManager, $id_question, $id_categorie): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(AddReponsesType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {

            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('addQuestion', ['id' => $id_categorie]);
        }

        return $this->render('add_quizz/reponse.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/add/quizz/{id}', name: 'addQuestion')]
    public function addQuestion(int $id, Request $request, EntityManagerInterface $entityManager)
    {
        $question = new Question();
        $form = $this->createForm(AddQuestionsType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {

            $entityManager->persist($question);
            $entityManager->flush();

            // return $this->redirectToRoute('app_addQuestion', ['id' => $id]);
            return $this->redirectToRoute('addReponse', [
                'id_question' => $question->getId(), 
                'id_categorie' => $id
            ]);
        }

        return $this->render('add_quizz/questions.html.twig', [
            'AddQuizzForm' => $form->createView(),
        ]);
    }



    #[Route('/add/quizz', name: 'add_quizz')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(AddQuizzType::class, $categorie);
        $form->handleRequest($request);
        trim($categorie->getName());

        if (($form->isSubmitted() && $form->isValid()))
        {

            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_addQuestion', ['id' => $categorie->getId()]);
        }
        else
        {
            return $this->render('add_quizz/index.html.twig', [
                'AddQuizzForm' => $form->createView(),
                'message' => 'Formulaire non valide',
            ]);
        }

        return $this->render('add_quizz/index.html.twig', [
            'AddQuizzForm' => $form->createView(),
        ]);
    }
}