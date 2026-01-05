<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\SecurityAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $rolet = ["ROLE_ADMIN"];
            $user->setRoles($rolet);
        /** @var string $plainPassword */
        $plainPassword = $form->get('plainPassword')->getData();
        $confirmPassword = $form->get('confirmPassword')->getData();

        // Vérification que le mot de passe et la confirmation correspondent
        if ($plainPassword !== $confirmPassword) {
            // Ajout d'une erreur au formulaire de confirmation du mot de passe
            $form->get('confirmPassword')->addError(new FormError('Passwords do not match.'));
        } else {
            $user->setFirstName(ucfirst(strtolower($user->getFirstName())));
            $user->setLastName(ucfirst(strtolower($user->getLastName())));
            // Encode le mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));


            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Registration successful! You can now log in.');

                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
