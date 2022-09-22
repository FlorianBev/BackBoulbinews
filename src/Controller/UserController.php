<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user', name: 'app_user')]
    /**
     * @Route("/inscription", name="user_register", methods={"GET|POST"})
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @return Response|RedirectResponse
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        # Instanciation d'un nouvel utilisateur
        $user = new User();
        $user->setRoles(["ROLE_USER"]);
        $user->setCreatedAt(new DateTime());
        $user->setUpdatedAt(new DateTime());

        # Création du formulaire
        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class, [
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'form-control',
                ],
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'form-control',
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'email@example.com',
                    'class' => 'form-control',
                ],
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Mot de passe',
                    'class' => 'form-control',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => [
                    'class' => 'd-block col-3 my-3 mx-auto btn btn-warning',
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            # Hash du password
            $user->setPassword(
                $passwordHasher->hashPassword($user, $user->getPassword())
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vous vous êtes inscris avec succès !');
            return $this->redirectToRoute("app_login");
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
