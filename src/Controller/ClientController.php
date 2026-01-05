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
            $role !== Constantes::ROLE_TAILLEUR && $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
                
            $clients = $entityManager->getRepository(User::class)->findBy(['isClient' => true]);
            $users = $entityManager->getRepository(User::class)->findBy(['isActive' => true]);
            $disabledUsers = $entityManager->getRepository(User::class)->findBy(['isActive' => false]);
                
                $newclient = new User();
            $addForm = $this->createForm(ClientType::class, $newclient);

            // Formulaire édition par utilisateur
            $editForms = [];
            foreach ($clients as $client) {
                $editForms[$client->getId()] = $this->createForm(ClientType::class, $client)->createView();
        }
                return $this->render('client/IndexClient.html.twig', [
                'clients' => $clients,
                'disabledUsers' => $disabledUsers,
                'form' => $addForm->createView(),
                'editForms' => $editForms,
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
        
        $user = new User();
        $user->setIsClient(true); // Par défaut, cet utilisateur sera un client
        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);
        
        // var_dump($user);
        if ($form->isSubmitted() && $form->isValid()) {
           

                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('telephone')->getData()
                );
                $user->setPassword($hashedPassword);
                $rolet = ["ROLE_CLIENT"];
                $user->setRoles($rolet);
               
               $usernameBase = $user->getFirstName() . '.' . $user->getLastName();

            $user->setUsername(
                $this->generateUniqueUsername($usernameBase, $entityManager)
            );

                $entityManager->persist($user);
                $entityManager->flush();
                
                    $this->addFlash('success', 'Client ajouté avec succès.');
                   return $this->redirectToRoute('ajout_mesurecl', array('client' => $user->getId()));
                    //return $this->redirectToRoute('app_choix', array('client' => $user->getId()));
            
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
       
        // Récupérer l'utilisateur à modifier
        $user = $entityManager->getRepository(User::class)->find($id);
       
        // Créer le formulaire pour l'utilisateur
        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe et le confirmer
            
               $usernameBase = $user->getFirstName() . '.' . $user->getLastName();

                $user->setUsername(
                    $this->generateUniqueUsername($usernameBase, $entityManager)
                );
                // Persist les modifications
            $entityManager->flush();
            $this->addFlash('success', 'Client mis à jour avec succès.');
            return $this->redirectToRoute('client_list');
               
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
         $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
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
#[Route('/desact_clients/{id}/desactiver', name: 'desact_client', methods: ['POST'])]
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

        return $this->redirectToRoute('list_user');
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