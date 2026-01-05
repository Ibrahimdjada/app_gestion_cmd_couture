<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\UserPermissionsType;
use App\Form\EditUserType;
use App\Constante\Constantes;
use App\Security\RoleChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private $userRepository;
  
    public function __construct(EntityManagerInterface $entityManager,
     UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        
    }
    #[Route('/aff_users', name: 'list_user')] 
    public function index(EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }

        $roles = $currentUser->getRoles();
        $role = $roles[0] ?? null;

        if ($role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER) {
            return $this->render('ErrorPage.html.twig');
        }

        // Utilisateurs actifs et désactivés
        $users = $entityManager->getRepository(User::class)->findBy(['isActive' => true]);
        $disabledUsers = $entityManager->getRepository(User::class)->findBy(['isActive' => false]);

        // Formulaire ajout
        $newUser = new User();
        $addForm = $this->createForm(UserType::class, $newUser);

        // Formulaire édition par utilisateur
        $editForms = [];
        foreach ($users as $user) {
            $editForms[$user->getId()] = $this->createForm(EditUserType::class, $user)->createView();
        }

        return $this->render('user/IndexUser.html.twig', [
            'users' => $users,
            'disabledUsers' => $disabledUsers,
            'form' => $addForm->createView(),
            'editForms' => $editForms,
        ]);
    }

    #[Route('/ajout_users/new', name: 'ajout_user')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $currentUser = $this->getUser();
        if (!$currentUser) return $this->redirectToRoute('app_login');

        $roles = $currentUser->getRoles();
        $role = $roles[0] ?? null;
        if ($role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER) {
            return $this->render('ErrorPage.html.twig');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($hashedPassword);
            // Récupérer le rôle sélectionné dans le formulaire
            $selectedRole = $form->get('role')->getData();
            if ($selectedRole === 'ROLE_TAILLEUR') {
                $user->setRoles(['ROLE_TAILLEUR']);
            } else {
                $user->setRoles(['ROLE_CLIENT']);
            }
            $usernameBase = $user->getFirstName() . '.' . $user->getLastName();

            $user->setUsername(
                $this->generateUniqueUsername($usernameBase, $entityManager)
            );

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur enregistré avec succès.');
            return $this->redirectToRoute('list_user');
        }

        return $this->render('user/FormUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mod_users/{id}/edit', name: 'edit_user')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditUserType::class, $user);

        $currentUser = $this->getUser();
        if (!$currentUser) return $this->redirectToRoute('app_login');

        $roles = $currentUser->getRoles();
        $role = $roles[0] ?? null;
        if ($role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER) {
            return $this->render('ErrorPage.html.twig');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usernameBase = $user->getFirstName() . '.' . $user->getLastName();

            $user->setUsername(
                $this->generateUniqueUsername($usernameBase, $entityManager)
            );

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('list_user');
        }

        return $this->render('user/EditUser.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

        #[Route('/aff_users/trash', name: 'list_user_trash')]
    public function trash(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findBy(['isActive' => false]);
        return $this->render('user/_trash_modal.html.twig', [
            'users' => $users
        ]);
    }

        #[Route('/activate_user/{id}', name: 'activate_user', methods: ['POST'])]
        public function activate(User $user, EntityManagerInterface $em): Response
        {
            $user->setIsActive(true);
            $user->setDeletedAt(null);
            $em->flush();
            return $this->redirectToRoute('list_user');
        }


    #[Route('/desact_users/{id}/desactiver', name: 'desact_user', methods: ['POST'])]
    public function desactiver(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }
        // Autoriser ADMIN et SUPER à faire la même action
        if (!RoleChecker::hasAny($currentUser, [Constantes::ROLE_ADMIN, Constantes::ROLE_SUPER])) {
            return $this->render('ErrorPage.html.twig');
        }

        // Empêcher la désactivation de l'utilisateur connecté
        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas désactiver votre propre compte.');
            return $this->redirectToRoute('list_user');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // ❌ au lieu de remove(), on désactive
            $user->setIsActive(false);
            $user->setDeletedAt(new \DateTime()); // optionnel si tu veux garder une date de désactivation

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur désactivé avec succès.');
        }

        return $this->redirectToRoute('list_user');
    }

    #[Route('/param/users/permissions', name: 'user_permissions')]
    public function permissionsList(UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $users = $userRepository->findAll();
        $users = $entityManager->getRepository(User::class)->findBy(['isActive' => true]);
        return $this->render('user/permissions_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/param/users/{id}/permissions', name: 'user_set_permissions', methods: ['GET','POST'])]
    public function setPermissions(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserPermissionsType::class, $user);
        $form->handleRequest($request);

        // Soumission AJAX dans le modal
        if ($form->isSubmitted() && $form->isValid()) {

            $perms = [];

            // Récupération dynamique selon ton form builder
            foreach ($form as $name => $field) {
                if ($field->getData()) {
                    $perms[] = $name;
                }
            }

            $user->setPermissions($perms);
            $em->flush();

            return new Response("OK");
        }

        return $this->render('user/_permissions_modal_content.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    private function generateUniqueUsername(string $base, EntityManagerInterface $em): string
    {
        $username = strtolower($base);
        $original = $username;
        $i = 1;

        // Vérifie si déjà utilisé
        while ($em->getRepository(User::class)->findOneBy(['username' => $username])) {
            $username = $original . $i;
            $i++;
        }

        return $username;
    }

}
