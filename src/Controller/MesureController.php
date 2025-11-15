<?php

namespace App\Controller;

use App\Entity\Mesure;
use App\Entity\User;
use App\Form\MesureType;
use App\Constante\Constantes;
use App\Form\MesureEditType;
use App\Form\MesureClientType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

class MesureController extends BaseController
{
    private EntityManagerInterface $entityManager;
    private $userRepository;
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }
    #[Route('/mesure', name: 'list_mesure')]
    public function index(): Response
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
        $mesures = $this->entityManager->getRepository(Mesure::class)->findAll();

        return $this->render('mesure/IndexMesure.html.twig', [
            'mesures' => $mesures,
        ]);
    }

    #[Route('/{client}/mesurecl/ajout', name: 'ajout_mesurecl')]
    public function ajoutercl(Request $request, EntityManagerInterface $entityManager, int $client): Response
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
        // Récupérer l'utilisateur correspondant à l'identifiant fourni dans la route
        $user = $entityManager->getRepository(User::class)->find($client);
    
        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('client_list'); // Redirigez vers une page appropriée
        }
    
        // Créer une nouvelle mesure
        $mesure = new Mesure();
        $mesure->setUser($user); // Associer l'utilisateur à la mesure
    
        // Créer un formulaire pour l'entité Mesure
        $form = $this->createForm(MesureClientType::class, $mesure);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Validation des valeurs numériques
                $fields = ['BasPantalon', 'Ceinture', 'Cuisse', 'EcartDos', 'Encolure', 'Epaule', 
                           'Fermeture', 'Longueur', 'LongueurPantalon', 'Manche', 'Poignee', 
                           'Poitrine', 'Taille', 'TourVentrale'];
    
                foreach ($fields as $field) {
                    $getter = 'get' . $field;
                    $value = $mesure->$getter();
    
                    if ($value < 0 || $value > 9.99) {
                        throw new \Exception("La valeur pour $field doit être comprise entre 0 et 9.99.");
                    }
                }

                foreach((new \ReflectionClass($mesure))->getProperties() as $property){
                    $property->setAccessible(True);
                    if
                    ($property->getValue($mesure)=== null)
                    {
                        $property->setValue($mesure,0);
                    }
                }
    
                // Persister et enregistrer
                $entityManager->persist($mesure);
                $entityManager->flush();
    
                $this->addFlash('success', 'La mesure a été ajoutée avec succès.');
                return $this->redirectToRoute('ajout_commandecl', ['client' => $user->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur : ' . $e->getMessage());
            }
        }
    
        return $this->render('mesure/FormMesureclient.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/mesure/ajout', name: 'ajout_mesure')]
    public function ajouter(Request $request): Response
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
        // Créer un nouveau client vide
        $mesure = new Mesure();

        // Créer un formulaire pour l'entité Client en utilisant ClientType
        $form = $this->createForm(MesureType::class, $mesure);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
            try {
                // Validation des valeurs numériques
                $fields = ['BasPantalon', 'Ceinture', 'Cuisse', 'EcartDos', 'Encolure', 'Epaule', 
                           'Fermeture', 'Longueur', 'LongueurPantalon', 'Manche', 'Poignee', 
                           'Poitrine', 'Taille', 'TourVentrale'];
    
                foreach ($fields as $field) {
                    $getter = 'get' . $field;
                    $value = $mesure->$getter();
    
                    if ($value < 0 || $value > 9.99) {
                        throw new \Exception("La valeur pour $field doit être comprise entre 0 et 9.99.");
                    }
                }
                
                foreach((new \ReflectionClass($mesure))->getProperties() as $property){
                    $property->setAccessible(True);
                    if
                    ($property->getValue($mesure)=== null)
                    {
                        $property->setValue($mesure,0);
                    }
                }

                // Enregistrer le nouveau client dans la base de données
                $this->entityManager->persist($mesure);
                $this->entityManager->flush();
               $this->addFlash('success', 'La mesure a été ajouté avec succès.');
               return $this->redirectToRoute('list_mesure');

            } catch (\Exception $e) {
                // Ajouter un flash message d'erreur générique
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du mesure verifier les champs.');
                // Vous pouvez aussi ajouter un message spécifique selon l'erreur, par exemple :
                // $this->addFlash('error', 'Erreur : ' . $e->getMessage());
            }
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, afficher le formulaire
        return $this->render('mesure/FormMesure.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/mesure/{id}/edit', name: 'edit_mesure')]

    public function edit(Request $request, Mesure $mesure): Response
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
        // Créer un formulaire pour l'édition du client en utilisant EditType
        $form = $this->createForm(MesureEditType::class, $mesure);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
               // Validation des valeurs numériques
                $fields = ['BasPantalon', 'Ceinture', 'Cuisse', 'EcartDos', 'Encolure', 'Epaule', 
                           'Fermeture', 'Longueur', 'LongueurPantalon', 'Manche', 'Poignee', 
                           'Poitrine', 'Taille', 'TourVentrale'];
    
                foreach ($fields as $field) {
                    $getter = 'get' . $field;
                    $value = $mesure->$getter();
    
                    if ($value < 0 || $value > 9.99) {
                        throw new \Exception("La valeur pour $field doit être comprise entre 0 et 9.99.");
                    }
                }
                
                foreach((new \ReflectionClass($mesure))->getProperties() as $property){
                    $property->setAccessible(True);
                    if
                    ($property->getValue($mesure)=== null)
                    {
                        $property->setValue($mesure,0);
                    }
                }
                // Enregistrer les modifications du client dans la base de données
                $this->entityManager->flush();

                // Ajouter un flash message de succès
                $this->addFlash('success', 'Les informations du mesure ont été mises à jour avec succès.');

                // Rediriger vers la liste des clients après la modification
                return $this->redirectToRoute('list_mesure');
            } catch (\Exception $e) {
                // En cas d'erreur lors de la mise à jour
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des informations du mesure.');
                // Vous pouvez ajouter un message spécifique à l'erreur si nécessaire
                // $this->addFlash('error', 'Erreur : ' . $e->getMessage());
            }
        }

        // Afficher le formulaire d'édition du client
        return $this->render('mesure/EditMesure.html.twig', [
            'form' => $form->createView(),
            'mesure' => $mesure,
        ]);
    }

    

    #[Route('/mesure/{id}/delete', name: 'delete_mesure')]

    public function delete(Mesure $mesure): Response
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
        $this->entityManager->remove($mesure);
        $this->entityManager->flush();

        // Ajouter un flash message de suppression réussie
        $this->addFlash('success', 'Le mesure a été supprimé avec succès.');

        // Redirection vers la liste des clients après la suppression
        return $this->redirectToRoute('list_mesure');
    }
}
