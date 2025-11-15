<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ClientType;
use App\Constante\Constantes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;

class ClientController extends BaseController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/aff_clients', name: 'client_list')]
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
                
            $clients = $entityManager->getRepository(User::class)->findBy(['isClient' => true]);
            
                return $this->render('client/IndexClient.html.twig', [
                'clients' => $clients,
            ]);
           
        }
    #[Route('/add_clients/add', name: 'ajout_client')]
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
            $role !== Constantes::ROLE_TAILLEUR && $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        $user = new User();
        $user->setIsClient(true); // Par défaut, cet utilisateur sera un client
        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);
        
        // var_dump($user);
        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('telephone')->getData()
                );
                $user->setPassword($hashedPassword);
                $rolet = ["ROLE_CLIENT"];
                $user->setRoles($rolet);
                $username = $user->getFirstName();
                $user->setUsername($username);
                $entityManager->persist($user);
                $entityManager->flush();
                
                    $this->addFlash('success', 'Client ajouté avec succès.');
                   return $this->redirectToRoute('ajout_mesurecl', array('client' => $user->getId()));
                    //return $this->redirectToRoute('app_choix', array('client' => $user->getId()));
            
                }  catch (\Exception $e) {
                    // Ajouter un flash message d'erreur générique
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du client.');
                    // Vous pouvez aussi ajouter un message spécifique selon l'erreur, par exemple :
                    // $this->addFlash('error', 'Erreur : ' . $e->getMessage());
                }
        }
                return $this->render('client/FormClient.html.twig', [
                    'form' => $form->createView(),
                ]);
    }

    #[Route('/add_clients/addMod', name: 'ajoutMod_client')]
    public function addModel(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
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
        $user = new User();
        $user->setIsClient(true); // Par défaut, cet utilisateur sera un client
        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);
        
        // var_dump($user);
        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('telephone')->getData()
                );
                $user->setPassword($hashedPassword);
                $rolet = ["ROLE_CLIENT"];
                $user->setRoles($rolet);
                $username = $user->getFirstName();
                $user->setUsername($username);
                $entityManager->persist($user);
                $entityManager->flush();
                
                    $this->addFlash('success', 'Client ajouté avec succès.');
                   return $this->redirectToRoute('ajout_commandecl', array('client' => $user->getId()));
                    //return $this->redirectToRoute('app_choix', array('client' => $user->getId()));
            
                }  catch (\Exception $e) {
                    // Ajouter un flash message d'erreur générique
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du client.');
                    // Vous pouvez aussi ajouter un message spécifique selon l'erreur, par exemple :
                    // $this->addFlash('error', 'Erreur : ' . $e->getMessage());
                }
        }
                return $this->render('client/FormClient.html.twig', [
                    'form' => $form->createView(),
                ]);
    }

    #[Route('/edit_clients/edit/{id}', name: 'edit_client')]
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
            $role !== Constantes::ROLE_TAILLEUR && $role !== Constantes::ROLE_USER && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        // Récupérer l'utilisateur à modifier
        $user = $entityManager->getRepository(User::class)->find($id);
       
        // Créer le formulaire pour l'utilisateur
        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe et le confirmer
            try {
                $username = $user->getFirstName();
            $user->setUsername($username);
                // Persist les modifications
            $entityManager->flush();
            $this->addFlash('success', 'Client mis à jour avec succès.');
            return $this->redirectToRoute('client_list');
                } catch (\Exception $e) {
                    // En cas d'erreur lors de la mise à jour
                    $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des informations du mesure.');
                    
                }
            }

        return $this->render('client/EditClient.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/sup_clients/delete/{id}', name: 'delete_client')]
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
    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé.');
    }

    $entityManager->remove($user);
    $entityManager->flush();

    $this->addFlash('success', 'Client supprimé avec succès.');

    return $this->redirectToRoute('client_list');
}
}