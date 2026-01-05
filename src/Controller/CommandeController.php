<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Rdv;
use App\Entity\User;
use App\Constante\Constantes;
use App\Form\CommandeType;
use App\Form\CommandeClientType;
use App\Form\CommandeEditType;
use App\Form\EditCmdReliquat;
use App\Form\EditCmdTailleur;
use App\Repository\CommandeRepository;
use App\Form\EditTailCmdType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Controller\BaseController;

class CommandeController extends BaseController
{
    private EntityManagerInterface $entityManager;
    private  $commandeRepository;
    private $userRepository;
    private $mesureRepository;

    public function __construct(EntityManagerInterface $entityManager,
        CommandeRepository $commandeRepository, UserRepository $userRepository, \App\Repository\MesureRepository $mesureRepository)
    {
        $this->entityManager = $entityManager;
        $this->commandeRepository = $commandeRepository;
        $this->userRepository = $userRepository;
        $this->mesureRepository = $mesureRepository;
    }

    #[Route('/commandes', name: 'list_commande')]
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
            $role !== Constantes::ROLE_TAILLEUR && $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        // Filtrage selon le rôle
        if ($role === Constantes::ROLE_TAILLEUR) {
            // Toutes les commandes non démarrées + celles où il est affecté (usert)
            $repo = $this->entityManager->getRepository(Commande::class);
            $commandesNonDemarre = $repo->findBy(['statut' => 'Non démarré']);
            $commandesPerso = $repo->findBy(['usert' => $users]);
            // Fusionner sans doublons
            $commandes = array_unique(array_merge($commandesNonDemarre, $commandesPerso), SORT_REGULAR);
        } else {
            $commandes = $this->entityManager->getRepository(Commande::class)->findAll();
        }
        $editForms = [];
        foreach ($commandes as $commande) {
            $editForms[$commande->getId()] = $this->createForm(CommandeEditType::class, $commande)->createView();
        }
        return $this->render('commande/IndexCommande.html.twig', [
            'commandes' => $commandes,
            'editForms' => $editForms,
            'mesureRepository' => $this->mesureRepository,
        ]);
    }

    #[Route('/commande/ajout', name: 'ajout_commande')]
    public function ajoutercommande(Request $request): Response
    {
        $users = $this->getUser();
        
        if (
            !$users 
        ) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        
        // Créer un nouveau Commande vide
        
        $commande = new Commande();
        $dateJour = new \DateTime(); // Date courante

        
        // Créer un formulaire pour l'entité Commande en utilisant CommandeType
        $form = $this->createForm(CommandeType::class, $commande);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Assigner la date courante à datCom
                $commande->setDatCom($dateJour);

                // Vérification de datRec
                $datRec = $form->get('datRec')->getData();
                if ($datRec === null) {
                    throw new \Exception('Veuillez saisir une date de récupération.');
                }
                if ($datRec < $dateJour) {
                    throw new \Exception('La date de récupération ne peut pas être antérieure à la date de commande.');
                }
               
                $montant = $commande->getMontant(); // Supposons que ce soit le getter pour montant
                $avances = $commande->getAvance(); // Supposons que ce soit le getter pour avances
                if ($montant <= 0 || $avances < 0 ) {
                    throw new \Exception('ce champs doivent être positifs.');
                }
                if ($avances > $montant) {
                    throw new \Exception('L\'avance ne doit pas dépasser le montant.');
                }    
                $reste = $montant - $avances;
                $commande->setReste($reste); // Setter pour le champ 'reste'
                $ast = ('Non démarré');
                $asp = (0);
                $asp = $ast;
                $commande->setStatut($asp); // Setter pour le champ 'statut'
               
                 // ========= UPLOAD MODELES =========
                $filesMod = $form->get('filemod')->getData();
                if (!empty($filesMod)) {
                    $commande->setFilemod($filesMod);
                    $commande->uploadMod();
                }

                // ========= UPLOAD TISSUS =========
                $filesTissu = $form->get('filetissu')->getData();
                if (!empty($filesTissu)) {
                    $commande->setFiletissu($filesTissu);
                    $commande->uploadTissu();
                }


                // Enregistrer la commande dans la base de données
                $this->entityManager->persist($commande);
                // Créer un nouvel objet Rdv lié à la commande
                $rdv = new Rdv();
                $rdv->setCommande($commande);
                // Enregistrer le nouvel Rdv dans la base de données
                $this->entityManager->persist($rdv);
                $this->entityManager->flush();

                // Ajouter un flash message de succès
                $this->addFlash('success', 'La commande a été ajoutée avec succès.');

                // Rediriger vers la liste des commandes après l'ajout
                return $this->redirectToRoute('list_rdv');
            } catch (\Exception $e) {
                // Ajouter un flash message d'erreur générique
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de la commande : ' . $e->getMessage());
            }
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, afficher le formulaire
        return $this->render('commande/FormCommande.html.twig', [
            'form' => $form->createView()
           
        ]);
    } 
    #[Route('/{client}/commandecl/ajout', name: 'ajout_commandecl')]
    public function ajoutercommandecl(Request $request): Response
    {
        $users = $this->getUser();
        
        if (
            !$users 
        ) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        
        // Créer un nouveau Commande vide
        $commande = new Commande();
        $dateJour = new \DateTime(); // Date courante

        // Créer un formulaire pour l'entité Commande en utilisant CommandeType
        $form = $this->createForm(CommandeClientType::class, $commande);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user_entity = $this->userRepository->findOneBy(['id' =>  $request->get('client')]);
                $commande->setUser($user_entity);
                // Assigner la date courante à datCom
                $commande->setDatCom($dateJour);

                // datRec sera déjà assigné correctement via le formulaire
                 // Vérification de datRec
                 $datRec = $form->get('datRec')->getData();
                 if ($datRec === null) {
                     throw new \Exception('Veuillez saisir une date de récupération.');
                 }
                 if ($datRec < $dateJour) {
                     throw new \Exception('La date de récupération ne peut pas être antérieure à la date de commande.');
                 }
                
                 $montant = $commande->getMontant(); // Supposons que ce soit le getter pour montant
                 $avances = $commande->getAvance(); // Supposons que ce soit le getter pour avances
                 if ($montant <= 0 || $avances < 0 ) {
                     throw new \Exception('ce champs doivent être positifs.');
                 }
                 if ($avances > $montant) {
                     throw new \Exception('L\'avance ne doit pas dépasser le montant.');
                 }    
                $reste = $montant - $avances;
                $commande->setReste($reste); // Setter pour le champ 'reste'
               
                $ast = ('Non démarré');
                    $asp = (0);
                    $asp = $ast;   
                $commande->setStatut($asp); // Setter pour le champ 'statut'

                 // ========= UPLOAD MODELES =========
                    $filesMod = $form->get('filemod')->getData();
                    if (!empty($filesMod)) {
                        $commande->setFilemod($filesMod);
                        $commande->uploadMod();
                    }

                    // ========= UPLOAD TISSUS =========
                    $filesTissu = $form->get('filetissu')->getData();
                    if (!empty($filesTissu)) {
                        $commande->setFiletissu($filesTissu);
                        $commande->uploadTissu();
                    }
                // Enregistrer la commande dans la base de données
                $this->entityManager->persist($commande);
                // Créer un nouvel objet Rdv lié à la commande
                $rdv = new Rdv();
                $rdv->setCommande($commande);
                // Vous pouvez aussi ajouter d'autres champs dans `Rdv` si nécessaire

                // Enregistrer le nouvel Rdv dans la base de données
                $this->entityManager->persist($rdv);
                $this->entityManager->flush();

                // Ajouter un flash message de succès
                $this->addFlash('success', 'La commande a été ajoutée avec succès.');

                // Rediriger vers la liste des commandes après l'ajout
                return $this->redirectToRoute('list_rdv');
            } catch (\Exception $e) {
                // Ajouter un flash message d'erreur générique
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de la commande : ' . $e->getMessage());
            }
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, afficher le formulaire
        return $this->render('commande/FormCommandeClient.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/{id}/edit', name: 'edit_commande')]

    public function edit(Request $request, Commande $commande): Response
    {
        $users = $this->getUser();
        
        if (
            !$users 
        ) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
       
        // Trouver le RDV associé à la commande
        $rdv = $this->entityManager->getRepository(Rdv::class)->findOneBy(['commande' => $commande]);

        // Créer un formulaire pour l'édition du Commande en utilisant EditType
        $form = $this->createForm(CommandeEditType::class, $commande);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Si la commande est modifiée, mettre à jour les champs de Rdv en conséquence
                if ($rdv) {
                    $rdv->setCommande($commande);
                    $this->entityManager->persist($rdv);
                }

                 // Vérification de datRec
                 $datRec = $form->get('datRec')->getData();
                 if ($datRec === null) {
                     throw new \Exception('Veuillez saisir une date de récupération.');
                 }
                 $dateJour=$commande->getDatCom();
                 if ($datRec < $dateJour) {
                     throw new \Exception('La date de récupération ne peut pas être antérieure à la date de commande.');
                 }
                
                 $montant = $commande->getMontant(); // Supposons que ce soit le getter pour montant
                 $avances = $commande->getAvance(); // Supposons que ce soit le getter pour avances
                 if ($montant <= 0 || $avances < 0 ) {
                     throw new \Exception('ce champs doivent être positifs.');
                 }
                 if ($avances > $montant) {
                     throw new \Exception('L\'avance ne doit pas dépasser le montant.');
                 }    
                $montant = $commande->getMontant(); // Supposons que ce soit le getter pour montant
                $avances = $commande->getAvance(); // Supposons que ce soit le getter pour avances
                $reste = $montant - $avances;
                $commande->setReste($reste); // Setter pour le champ 'reste'
               
                // Enregistrer les modifications du Commande dans la base de données
                $this->entityManager->flush();

                // Ajouter un flash message de succès
                $this->addFlash('success', 'Les informations du commande ont été mises à jour avec succès.');

                // Rediriger vers la liste des Commandes après la modification
                return $this->redirectToRoute('list_commande');
            } catch (\Exception $e) {
                // En cas d'erreur lors de la mise à jour
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des informations du Commande.');
                // Vous pouvez ajouter un message spécifique à l'erreur si nécessaire
                // $this->addFlash('error', 'Erreur : ' . $e->getMessage());
            }
        }

        // Afficher le formulaire d'édition du Commande
        return $this->render('commande/EditCommande.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
        ]);
    }

    #[Route('/commande/{id}/annuler', name: 'annuler_commande')]
    public function annuler(Commande $commande, Request $request): Response
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
        // Si paramètre restore=1, on restaure la commande
        if ($request->query->get('restore')) {
            $commande->setStatut('Non demarré');
            $commande->setDeletedAt(null);
            $this->entityManager->flush();
            $this->addFlash('success', 'La commande a été restaurée avec succès.');
            return $this->redirectToRoute('liste_commandes_annulees');
        }
        // Sinon, on annule
        $commande->setStatut('Annulée');
        $commande->setDeletedAt(new \DateTime());
        $this->entityManager->flush();
        $this->addFlash('success', 'La commande a été annulée avec succès.');
        return $this->redirectToRoute('list_commande');
    }

    #[Route('/commandes/annulees', name: 'liste_commandes_annulees')]
    public function listeCommandesAnnulees(): Response
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
        $commandes = $this->commandeRepository->findBy(['statut' => 'Annulée']);
        return $this->render('commande/liste_commandes_annulees.html.twig', [
            'commandes' => $commandes
        ]);
    }
    #[Route('/commande/{id}/supprimer', name: 'supprimer_commande')]
    public function supprimer(Commande $commande): Response
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
        // Supprimer tous les rdv liés à cette commande
        $rdvs = $this->entityManager->getRepository(Rdv::class)->findBy(['commande' => $commande]);
        foreach ($rdvs as $rdv) {
            $this->entityManager->remove($rdv);
        }
        $this->entityManager->remove($commande);
        $this->entityManager->flush();
        $this->addFlash('success', 'La commande a été supprimée définitivement.');
        return $this->redirectToRoute('liste_commandes_annulees');
    }
    #[Route('/cmdrel/{id}', name: 'cmd_reliquat')]
    public function cmdrel(Request $request, Commande $commande): Response
    {
    $users = $this->getUser();
    if (!$users) {
    return $this->redirectToRoute('app_login');
    }
    $roles = $users->getRoles();
    $role = $roles[0];
    if (
    $role !== Constantes::ROLE_TAILLEUR &&
    $role !== Constantes::ROLE_ADMIN &&
    $role !== Constantes::ROLE_SUPER
    ) {
    return $this->render('ErrorPage.html.twig');
    }

    $form = $this->createForm(EditCmdReliquat::class, $commande);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    try {
        $reliquat = $commande->getReliquat();
        $reste    = $commande->getReste();

        if ($reliquat < 0) {
            throw new \Exception('ce champs doivent être positifs.');
        }
        if ($reliquat > $reste) {
            throw new \Exception('Le reliquat est trop grand et dépasse le reste à payer.');
        }

        $commande->setReste($reste - $reliquat);
        $commande->setAvance($commande->getAvance() + $reliquat);

        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le reliquat a été ajouté avec succès.');

        return $this->redirectToRoute('list_commande');
    } catch (\Exception $e) {
        $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des informations du commande.');
    }
    }

    return $this->render('commande/cmdReliquat-form-edit.html.twig', [
    'form' => $form->createView(),
    'commande' => $commande,
    ]);
    }


    #[Route('/cmdtailleur/{id}', name: 'cmd_affectation', methods: ['GET', 'POST'])]
    public function commandeTailleur(Request $request, Commande $commande): Response
    {
        $users = $this->getUser();
        if (!$users) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        // Si tailleur connecté, auto-affectation avec confirmation
        if ($role === Constantes::ROLE_TAILLEUR) {
            if ($request->isMethod('POST')) {
                try {
                    // Affecter le tailleur connecté à la commande
                    $commande->setUsert($users);
                    $commande->setStatut('Encours');
                    // dd($commande);
                    $this->entityManager->persist($commande);
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Vous avez été affecté à la commande avec succès.');
                    return $this->redirectToRoute('list_commande');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'affectation : ' . $e->getMessage());
                }
            }
            // Afficher confirmation simple
            return $this->render('commande/cmdtailleur-confirm.html.twig', [
                'commande' => $commande
            ]);
        }
        // Sinon, admin/super : formulaire classique
        if ($role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER) {
            return $this->render('ErrorPage.html.twig');
        }
        $form = $this->createForm(EditCmdTailleur::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $commande->setStatut('Encours');
                $this->entityManager->persist($commande);
                $this->entityManager->flush();
                $this->addFlash('success', 'Le tailleur a été affecté avec succès.');
                return $this->redirectToRoute('list_commande');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des informations du commande.');
            }
        }
        return $this->render('commande/cmdtailleur-form-edit.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
        ]);
    }


    
    #[Route('/commande/{id}', name: 'commande_terminer')]

    public function commandeterminer(Request $request,EntityManagerInterface $entityManager,   commande $commande): Response

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
        $dateJour = date('Y-m-d');
        
        $ast = ('Terminer');
        $asp = (2);
        $asp = $ast;
        $commande->setStatut($asp);

        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirectToRoute('list_commande');
    }

    #[Route('/commande/{id}', name: 'commande_recuperer')]

    public function commanderecuperer(Request $request,EntityManagerInterface $entityManager,   commande $commande): Response

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
        $dateJour = date('Y-m-d');
        $ast = ('Livrer');
        $asp = (3);
        $asp = $ast;
        $commande->setStatut($asp);

        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirectToRoute('list_commande');
    }
    //Affichage le nombre concerner
    public function getCommandeAllStatut(): Response
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
            $role == Constantes::ROLE_TAILLEUR && $role == Constantes::ROLE_ADMIN && $role == Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        // $entityManager = $this->getDoctrine()->getManager();
        $result = $this->commandeRepository->getCommandeAllStatut('Non demarré');

        if ($result) {
            $statut = intval($result[0]['nombre']);
            $response = new Response($statut);
            return $response;
        }

        return new Response(0);
    }
    public function getCommande1AllStatut(): Response
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
        // $entityManager = $this->getDoctrine()->getManager();
        $result = $this->commandeRepository->getCommandeAllStatut('Encours');
        if ($result) {
            $statut = intval($result[0]['nombre']);
            $response = new Response($statut);
            return $response;
        }
        return new Response(0);
    }
    public function getCommande2AllStatut(): Response
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
        // $entityManager = $this->getDoctrine()->getManager();
        $result = $this->commandeRepository->getCommandeAllStatut('Terminer');
        if ($result) {
            $statut = intval($result[0]['nombre']);
            $response = new Response($statut);
            return $response;
        }
        return new Response(0);
    }
    public function getCommande3AllStatut(): Response
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
        // $entityManager = $this->getDoctrine()->getManager();
        $result = $this->commandeRepository->getCommandeAllStatut('Livrer');
        if ($result) {
            $statut = intval($result[0]['nombre']);
            $response = new Response($statut);
            return $response;
        }
        return new Response(0);
    }

    //Affichage la liste concerner
    
    #[Route('/cmdaffNonDem', name: 'cmd_aff_NonDem')]

    public function cmdaff(): Response
    {
        $users = $this->getUser();
        if (!$users) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        $resultat = "";
        $classe = "";
        // Admin/super/tailleur peuvent voir toutes les commandes non démarrées
        $commande = $this->commandeRepository->findBy(['statut' => 'Non demarré']);
        return $this->render('commande/indexCmdAffNonDemarre.html.twig', [
            'commande' => $commande,
            'resultat' => $resultat,
            'classe' => $classe
        ]);
    }
    
    #[Route('/cmdaffEncours', name: 'cmd_aff_Encours')]
    public function cmdaff1(): Response
    {
        $users = $this->getUser();
        if (!$users) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        $resultat = "";
        $classe = "";
        if ($role === Constantes::ROLE_TAILLEUR) {
            $commande = $this->commandeRepository->findBy(['statut' => 'Encours', 'usert' => $users]);
        } else {
            $commande = $this->commandeRepository->findBy(['statut' => 'Encours']);
        }
        return $this->render('commande/indexCmdAffEncours.html.twig', [
            'commande' => $commande,
            'resultat' => $resultat,
            'classe' => $classe
        ]);
    }
    
    #[Route('/cmd_affTerminer', name: 'cmd_aff_Terminer')]
    public function cmdaff2(): Response
    {
        $users = $this->getUser();
        if (!$users) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        $resultat = "";
        $classe = "";
        if ($role === Constantes::ROLE_TAILLEUR) {
            $commande = $this->commandeRepository->findBy(['statut' => 'Terminer', 'usert' => $users]);
        } else {
            $commande = $this->commandeRepository->findBy(['statut' => 'Terminer']);
        }
        return $this->render('commande/indexCmdAffTerminer.html.twig', [
            'commande' => $commande,
            'resultat' => $resultat,
            'classe' => $classe
        ]);
    }
   
    #[Route('/cmd_affLivrer', name: 'cmd_aff_Livrer')]
    public function cmdaff3(): Response
    {
        $users = $this->getUser();
        if (!$users) {
            return $this->redirectToRoute('app_login');
        }
        $roles = $users->getRoles();
        $role = $roles[0];
        $resultat = "";
        $classe = "";
        if ($role === Constantes::ROLE_TAILLEUR) {
            $commande = $this->commandeRepository->findBy(['statut' => 'Livrer', 'usert' => $users]);
        } else {
            $commande = $this->commandeRepository->findBy(['statut' => 'Livrer']);
        }
        return $this->render('commande/indexCmdAffLivrer.html.twig', [
            'commande' => $commande,
            'resultat' => $resultat,
            'classe' => $classe
        ]);
    }
    
    #[Route('/recu/{id}', name: 'faire_recu')]
    public function fairerecu(Commande $id): Response
    {
        $user = $this->getUser();
        $commande = $this->commandeRepository->findOneById($id);
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
        # return new Response($commande->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$commande) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('recu/recu.html.twig', [
            'commande' => $commande,
            'resultat' => $resultat,
            'classe' => $classe
        ]);
    }
    #[Route('/mesure-client-table/{userId}', name: 'mesure_client_table')]
    public function mesureClientTable($userId): Response
    {
        $mesures = $this->mesureRepository->findBy(['user' => $userId], ['id' => 'DESC']);
        return $this->render('commande/_mesure_client_table.html.twig', [
            'mesures' => $mesures
        ]);
    }
}
