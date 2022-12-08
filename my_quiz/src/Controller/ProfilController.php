<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditPasswordType;
use App\Form\EditProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{

    #[Route('/profil', name: 'profil')]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }
    
    #[Route('/profil/edit/password', name: 'editPassword')]
    public function editPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        if($this->getUser() !== $user)
        {
            return $this->redirectToRoute('main');
        }

        $form = $this->createForm(EditPasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $em->persist($user);
            $em->flush();

            $user = $form->getData();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'message',
                'Vos informations ont été bien modifiées!'
            );

            return $this->redirectToRoute('profil');
        }

        return $this->render('profil/editProfil.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/profil/edit', name: 'editProfil')]
    public function editProfil(ManagerRegistry $doctrine, Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Votre profil a bien été modifié');
            return $this->redirectToRoute('profil');
        }
        return $this->render('profil/editProfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profil/delete', name: 'deleteProfil')]
    public function delete(ManagerRegistry $doctrine): Response
    {
        $id = $this->getUser();
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_register');
    }
}