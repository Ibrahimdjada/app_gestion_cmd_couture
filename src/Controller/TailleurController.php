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
            $role !== Constantes::ROLE_TAILLEUR && $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
            $tailleurs = $entityManager->getRepository(User::class)->findBy(['isTailleur' => true]);

            return $this->render('tailleur/IndexTailleur.html.twig', [
                'tailleurs' => $tailleurs,
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
             $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        $user = new User();
        $user->setIsTailleur(true); // Par défaut, cet utilisateur sera un tailleur
        
        $form = $this->createForm(TailleurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('telephone')->getData()
                );
                $user->setPassword($hashedPassword);

                $rolet = ["ROLE_TAILLEUR","ROLE_CLIENT"];
                $user->setRoles($rolet);
                $username = $user->getFirstName();
                $user->setUsername($username);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'tailleur ajouté avec succès.');

                return $this->redirectToRoute('tailleur_list');
            
        }  catch (\Exception $e) {
            // Ajouter un flash message d'erreur générique
            $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du tailleur.');
          
        }
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
             $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
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
            try {
                $username = $user->getFirstName();
                $user->setUsername($username);
                // Persist les modifications
            $entityManager->flush();
            $this->addFlash('success', 'tailleur mis à jour avec succès.');
            return $this->redirectToRoute('tailleur_list');
                } catch (\Exception $e) {
                    // En cas d'erreur lors de la mise à jour
                    $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des informations du mesure.');
                    
                }
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
             $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
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
}