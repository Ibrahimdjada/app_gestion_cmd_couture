<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\EditUserType;
use App\Constante\Constantes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;


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
        $users = $this->getUser();
        
        if (
            !$users 
        ) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        if (
            $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        // Récupérer tous les utilisateurs
        $users = $entityManager->getRepository(User::class)->findAll();
        
       
        return $this->render('user/IndexUser.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/ajout_users/new', name: 'ajout_user')]
    public function new(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $users = $this->getUser();
        
        if (
            !$users 
        ) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if (
            $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);
            $rolet = ["ROLE_USER","ROLE_TAILLEUR","ROLE_CLIENT"];
            $user->setRoles($rolet);
            $username = $user->getFirstName();
            $user->setUsername($username);
            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur enregistré avec succer.');

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
        $users = $this->getUser();
        
        if (
            !$users 
        ) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        if (
            $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les changements de l'utilisateur
            $username = $user->getFirstName();
            $user->setUsername($username);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succer.');

            return $this->redirectToRoute('list_user');
        }

        return $this->render('user/EditUser.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/sup_users/{id}/delete', name: 'delete_user', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $users = $this->getUser();
        
        if (
            !$users 
        ) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        if (
            $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Supprimer l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succer.');
        }

        return $this->redirectToRoute('list_user');
    }

    // #[Route('/autocomplete-users', name: 'autocomplete_users', methods: ['POST'])]
    // public function autocompleteUsers(Request $request): JsonResponse
    // {
    //     $data = json_decode($request->getContent(),true);
    //     $query = $data['query'] ??'';

    //     $users= $this->userRepository->createQueryBuilder('u')
    //     ->select("concat(u.firstName,'',u.lastName) AS fullName")
    //         ->Where('u.isClient=true')
    //         ->andWhere("concat(u.firstName,'', u.lastName) LIKE:query")
    //         ->setParameter('query', '%' .$query. '%')
    //         ->getQuery()
    //         ->getArrayResult();
        

    //     return $this->json(array_column($users,'fullName'));
    // }
}
