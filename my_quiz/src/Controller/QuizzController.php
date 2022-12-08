<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

$_SESSION['score'] = 0;

class QuizzController extends AbstractController
{
    #[Route('/quizz/{id}', name: 'quizz')]
    public function index(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
        $questions = $entityManager
            ->getRepository(Question::class)
            ->findBy(['id_categorie' => $id]);
        $question_id = $questions[0]->getId();
        $score = 0;
        return $this->render('quizz/index.html.twig', [
            'categorie' => $categorie,
            'question_id' => $question_id,
            'score' => $score,
        ]);
    }

    #[Route('/quizz/{id}/{id_question}/{score}', name: 'quizz_run')]
    public function showQuestions(
        EntityManagerInterface $manager,
        $id,
        $id_question,
        $score
    ): Response {
        if (isset($_POST['reponse'])) {
            $reponse = $manager
                ->getRepository(Reponse::class)
                ->find($_POST['reponse']);
            if ($reponse->isReponseExpected() == 1) {
                $score++;
            } else {
                $score = $score;
            }
        }

        $categorie = $manager->getRepository(Categorie::class)->find($id);
        $question = $manager
            ->getRepository(Question::class)
            ->find($id_question);
        $reponses = $manager
            ->getRepository(Reponse::class)
            ->findBy(['id_question' => $id_question]);
        $count_question = count(
            $manager
                ->getRepository(Question::class)
                ->findBy(['id_categorie' => $id])
        );
        $correct_answer = '';

        $correct_answer = $manager->getRepository(Reponse::class)->findOneBy([
            'id_question' => $id_question,
            'reponse_expected' => 1,
        ]);

        return $this->render('quizz/quizz.html.twig', [
            'categorie' => $categorie,
            'question' => $question,
            'reponses' => $reponses,
            'id_question' => $id_question,
            'count_question' => $count_question,
            'score' => $score,
            'correct_answer' => $correct_answer,
        ]);
    }
    #[Route('/quizz/{id}/{id_question}/{score}/end', name: 'quizz_end')]
    public function endQuizz(
        EntityManagerInterface $manager,$id,$id_question,$score): Response {
        if (isset($_POST['reponse'])) {
            $reponse = $manager->getRepository(Reponse::class)->find($_POST['reponse']);
            if ($reponse->isReponseExpected() == 1) {
                $score++;
            } else {
                $score = $score;
            }
        }
        $categorie = $manager->getRepository(Categorie::class)->find($id);
        $question = $manager
            ->getRepository(Question::class)
            ->find($id_question);
        $count_question = count(
            $manager
                ->getRepository(Question::class)
                ->findBy(['id_categorie' => $id])
        );

        return $this->render('quizz/end.html.twig', [
            'categorie' => $categorie,
            'question' => $question,
            'id_question' => $id_question,
            'count_question' => $count_question,
            'score' => $score,
        ]);
    }
}
