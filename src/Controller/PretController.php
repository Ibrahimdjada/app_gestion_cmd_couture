<?php

namespace App\Controller;

use App\Entity\Pret;
use App\Form\PretType;
use App\Constante\Constantes;
use App\Form\EditPretType;
use App\Form\EditpretReliquat;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PretRepository;


class PretController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/pret', name: 'list_pret')]
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
             $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        $prets = $this->entityManager->getRepository(Pret::class)->findAll();

        return $this->render('dette/pret.html.twig', [
            'prets' => $prets,
        ]);
    }

    #[Route('/pret/ajout', name: 'ajout_pret')]
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
        
        // Créer un nouveau client vide
        $pret = new Pret();

        // Créer un formulaire pour l'entité Client en utilisant ClientType
        $form = $this->createForm(PretType::class, $pret);
        $dateJour = new \DateTime();
        // var_dump($pret);
        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                
                
                $pret->setDatP($dateJour);
                $datP = $pret->getDatP();
                $datEch = $form->get('datEch')->getData();
                if ($datEch === null) {
                    throw new \Exception('Veuillez saisir une date d\'echéance.');
                }
                if ($datEch < $datP) {
                    throw new \Exception('La date d\'echéance ne peut pas être antérieure à la date d\'emprunt.');
                }
                $psnp = ('Non payer');
                $psn = (0);
                $psn = $psnp;
                $pret->setStat($psnp);

                $montant = $pret->getMntP(); // Supposons que ce soit le getter pour montant
                $periode = $pret->getPrd();
                if ($montant <= 0 || $periode < 0 ) {
                    throw new \Exception(' ce champs doivent être positifs.');
                } 
                $montant = $pret->getMntP();
                
                $div = $montant / $periode;
                $pret->setMs($div);
                $reste = $montant;
                $pret->setReste($reste);
                // Enregistrer le nouveau client dans la base de données
                $this->entityManager->persist($pret);
                $this->entityManager->flush();

                // Ajouter un flash message de succès
                $this->addFlash('success', 'La pret a été ajouté avec succès.');

                // Rediriger vers la liste des clients après l'ajout
                return $this->redirectToRoute('list_pret');
            } catch (\Exception $e) {
                // Ajouter un flash message d'erreur générique
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du pret.');
                // Vous pouvez aussi ajouter un message spécifique selon l'erreur, par exemple :
                // $this->addFlash('error', 'Erreur : ' . $e->getMessage());
            }
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, afficher le formulaire
        return $this->render('dette/pret-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/pret/{id}/edit', name: 'edit_pret')]

    public function edit(Request $request, Pret $pret): Response
    {
        $users = $this->getUser();
        
        if (
            !$users 
        ) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        
        // Créer un formulaire pour l'édition du client en utilisant EditType
        $form = $this->createForm(EditPretType::class, $pret);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                 // Vérification de datRec
                 $datP = $pret->getDatP();
                 $datEch = $form->get('datEch')->getData();
                if ($datEch === null) {
                    throw new \Exception('Veuillez saisir une date d\'echéance.');
                }
                if ($datEch < $datP) {
                    throw new \Exception('La date d\'echéance ne peut pas être antérieure à la date d\'emprunt.');
                }
                
                 $montant = $pret->getMntP(); // Supposons que ce soit le getter pour montant
                 $periode = $pret->getPrd(); // Supposons que ce soit le getter pour avances
                 if ($montant <= 0 || $periode < 0 ) {
                    throw new \Exception(' ce champs doivent être positifs.');
                } 
               
                $div = $montant / $periode;
                $pret->setMs($div);
                $reste = $montant;
            $pret->setReste($reste);
                // Enregistrer les modifications du client dans la base de données
                $this->entityManager->flush();

                // Ajouter un flash message de succès
                $this->addFlash('success', 'Les informations du pret ont été mises à jour avec succès.');

                // Rediriger vers la liste des clients après la modification
                return $this->redirectToRoute('list_pret');
            } catch (\Exception $e) {
                // En cas d'erreur lors de la mise à jour
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des informations du pret.');
                // Vous pouvez ajouter un message spécifique à l'erreur si nécessaire
                // $this->addFlash('error', 'Erreur : ' . $e->getMessage());
            }
        }

        // Afficher le formulaire d'édition du client
        return $this->render('dette/pret-form-edit.html.twig', [
            'form' => $form->createView(),
            'pret' => $pret,
        ]);
    }

    #[Route('/pret/{id}/delete', name: 'delete_pret')]

    public function delete(Pret $pret): Response
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
        $this->entityManager->remove($pret);
        $this->entityManager->flush();

        // Ajouter un flash message de suppression réussie
        $this->addFlash('success', 'Le pret a été supprimé avec succès.');

        // Redirection vers la liste des clients après la suppression
        return $this->redirectToRoute('list_pret');
    }
    
    #[Route('/pret_versement/{id}', name: 'pret_payer')]

    public function pretversement(Request $request, Pret $pret): Response
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
        // Créer un formulaire pour l'édition du client en utilisant EditType
        $form = $this->createForm(EditpretReliquat::class, $pret);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $pst = ('Non payer');
                    $psp = (0);
                    $psp = $pst;
                    $pret->setStat($psp);
                   
                    $montant = $pret->getMntP(); 
        $restes = $pret->getReste(); 
        $rel = $pret->getReliquat();

        if ($rel < 0) {
            throw new \Exception('Le reliquat doit être positif.');
        }

        if ($restes > $montant) {
            throw new \Exception('Le reste ne doit pas dépasser le montant.');
        }

        if ($rel > $restes) {
            throw new \Exception('Le reliquat est trop grand et dépasse le reste à payer.');
        }

        // Mise à jour du reste après prise en compte du reliquat.
        $rest = $restes - $rel;
        $pret->setReste($rest);

        if ($rest == 0) {
            // Si le reste est zéro, marquer comme "Payer".
            $pret->setStat('Payer');
        }
                
                // Enregistrer les modifications du client dans la base de données
                $this->entityManager->persist($pret);
                $this->entityManager->flush();

                // Ajouter un flash message de succès
                $this->addFlash('success', 'le reliquat a ete ajouter avec succès.');

                // Rediriger vers la liste des clients après la modification
                return $this->redirectToRoute('list_pret');
            } catch (\Exception $e) {
                // En cas d'erreur lors de la mise à jour
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des informations du pret.');
                // Vous pouvez ajouter un message spécifique à l'erreur si nécessaire
                // $this->addFlash('error', 'Erreur : ' . $e->getMessage());
            }
        }

        // Afficher le formulaire d'édition du client
        return $this->render('dette/PretReliquat-form-edit.html.twig', [
            'form' => $form->createView(),
            'pret' => $pret,
        ]);
    }
 
}
