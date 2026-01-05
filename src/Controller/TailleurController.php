<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\TailleurType;
use App\Constante\Constantes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class TailleurController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/aff_tailleurs', name: 'tailleur_list')]
        public function list(EntityManagerInterface $entityManager): Response
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
            $role !== Constantes::ROLE_TAILLEUR && $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
            $tailleurs = $entityManager->getRepository(User::class)->findBy(['isTailleur' => true]);
            $users = $entityManager->getRepository(User::class)->findBy(['isActive' => true]);
            $disabledUsers = $entityManager->getRepository(User::class)->findBy(['isActive' => false]);

            // Formulaire ajout
        $newtailleur = new User();
        $addForm = $this->createForm(TailleurType::class, $newtailleur);

        // Formulaire édition par utilisateur
        $editForms = [];
        foreach ($tailleurs as $tailleur) {
            $editForms[$tailleur->getId()] = $this->createForm(TailleurType::class, $tailleur)->createView();
        }
            return $this->render('tailleur/IndexTailleur.html.twig', [
                'tailleurs' => $tailleurs,
                'disabledUsers' => $disabledUsers,
            'form' => $addForm->createView(),
            'editForms' => $editForms,
            ]);
        }
    #[Route('/ajout_tailleurs/add', name: 'ajout_tailleur')]
    public function add(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
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
             $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        $user = new User();
        $user->setIsTailleur(true); // Par défaut, cet utilisateur sera un tailleur
        
        $form = $this->createForm(TailleurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           

                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('telephone')->getData()
                );
                $user->setPassword($hashedPassword);

                $rolet = ["ROLE_TAILLEUR"];
                $user->setRoles($rolet);
                $usernameBase = $user->getFirstName() . '.' . $user->getLastName();

                $user->setUsername(
                    $this->generateUniqueUsername($usernameBase, $entityManager)
                );

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'tailleur ajouté avec succès.');

                return $this->redirectToRoute('tailleur_list');
            
        }  
        
   
        return $this->render('tailleur/FormTailleur.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/mod_tailleurs/edit/{id}', name: 'edit_tailleur')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
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
             $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        // Récupérer l'utilisateur à modifier
        $user = $entityManager->getRepository(User::class)->find($id);
        
        // Créer le formulaire pour l'utilisateur
        $form = $this->createForm(TailleurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe et le confirmer
            
                $usernameBase = $user->getFirstName() . '.' . $user->getLastName();

                $user->setUsername(
                    $this->generateUniqueUsername($usernameBase, $entityManager)
                );
                // Persist les modifications
            $entityManager->flush();
            $this->addFlash('success', 'tailleur mis à jour avec succès.');
            return $this->redirectToRoute('tailleur_list');
               
            }

        return $this->render('tailleur/EditTailleur.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/sup_tailleurs/delete/{id}', name: 'delete_tailleur')]
public function delete(EntityManagerInterface $entityManager, int $id): Response
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
             $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        $user = $entityManager->getRepository(User::class)->find($id);
        $users = $this->getUser();
            $roles = $users->getRoles();
            $role = $roles[0];
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

    $entityManager->remove($user);
    $entityManager->flush();

    $this->addFlash('success', 'tailleur supprimé avec succès.');

    return $this->redirectToRoute('tailleur_list');
}
#[Route('/desact_tailleurs/{id}/desactiver', name: 'desact_tailleur', methods: ['POST'])]
    public function desactiver(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $users = $this->getUser();

        if (!$users) {
            return $this->redirectToRoute('app_login');
        }

        $roles = $users->getRoles();
        $role = $roles[0];

        if ($role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER) {
            return $this->render('ErrorPage.html.twig');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {

            // ❌ au lieu de remove(), on désactive
            $user->setIsActive(false);
            $user->setDeletedAt(new \DateTime()); // optionnel si tu veux garder une date de désactivation

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur désactivé avec succès.');
        }

        return $this->redirectToRoute('tailleur_list');
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