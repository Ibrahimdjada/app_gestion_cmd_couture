<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Repository\CommandeRepository;
use App\Form\RapporttailType;
use App\Form\RapportType;
use App\Form\RapportconcType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RapportController extends AbstractController
{
    private $commandeRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        CommandeRepository $commandeRepository,
        )
    {
        $this->entityManager = $entityManager;
        $this->commandeRepository = $commandeRepository;
    }

    #[Route('/periode', name: 'rap_peride')]
    public function rapPeriode(Request $request): Response
    {
        
        $users = $this->getUser();
   
        $roles = $users->getRoles();
        $role = $roles[0];
        $resultat = "";
        $classe = "";
        $dateJour = date('Y-m-d');
        $datDebut = "";
        $datFin = "";
        $form = $this->createForm(RapporttailType::class);
        if (
            $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            // Récupération des valeurs directement depuis la requête
            $datDebut = $request->get('rapporttail')['datDebut'];
            $datFin = $request->get('rapporttail')['datFin'];
            // Conversion en \DateTime si nécessaire
            $datDebut = new \DateTime($datDebut);
            $datFin = new \DateTime($datFin);
            // Appeler la méthode du repository avec ces données
            $commande = $this->commandeRepository->getRapTail($datDebut, $datFin);
            return $this->render('rapport/rapportparperiode.html.twig', [
            'commande' => $commande,
            'resultat' => $resultat,
            'classe' => $classe,
            'form' => $form->createView(),
            ]);
        } else {
            return $this->render('rapport/rapportparperiode.html.twig', [
                'commande' => null,
                'resultat' => $resultat,
                'classe' => $classe,
                'form' => $form->createView()
            ]);
        }
    }
    #[Route('/concepteur', name: 'rap_conc')]
    public function rapconc(Request $request): Response
    {
        $users = $this->getUser();
        $resultat = "";
        $classe = "";
        $roles = $users->getRoles();
        $role = $roles[0];
        $dateJour = date('Y-m-d');
        $datDebut = "";
        $datFin = "";
        $usert = "";


        $form = $this->createForm(RapportconcType::class);
        if (
            $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');

        }
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data=$form->getData();

            $datDebut = $data['datDebut']?? null;
            $datFin =$data['datFin']?? null;
            $usert = $data['usert']?? null;
          
            $commande = $this->commandeRepository->getRapConc($datDebut, $datFin, $usert);
            
            return $this->render('rapport/rapportparconcepteur.html.twig', [
                'commande' => $commande,
                'resultat' => $resultat,
                'classe' => $classe,
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('rapport/rapportparconcepteur.html.twig', [
                'commande' => null,
                'resultat' => $resultat,
                'classe' => $classe,
                'form' => $form->createView()
            ]);
        }
    }
    #[Route('/statut', name: 'rap_statut')]
    public function rapStatut(Request $request): Response
    {
        $users = $this->getUser();
        $resultat = "";
        $classe = "";
        $roles = $users->getRoles();
        $role = $roles[0];
        $dateJour = date('Y-m-d');
        $datDebut = "";
        $datFin = "";
        $stat = "";


        $form = $this->createForm(RapportType::class);
        if (
            $role !== Constantes::ROLE_ADMIN && $role !== Constantes::ROLE_SUPER
        ) {
            return $this->render('ErrorPage.html.twig');
        }
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            // Récupération des valeurs directement depuis la requête
            $datDebut = $request->get('rapport')['datDebut'];
            $datFin = $request->get('rapport')['datFin'];
            $stat =$request->get('rapport')['stat'];
            // Conversion en \DateTime si nécessaire
            $datDebut = new \DateTime($datDebut);
            $datFin = new \DateTime($datFin);
            


            $commande = $this->commandeRepository->getRapPeriode($datDebut, $datFin, $stat);
           
            return $this->render('rapport/rapportparstatut.html.twig', [
                'commande' => $commande,
                'resultat' => $resultat,
                'classe' => $classe,
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('rapport/rapportparstatut.html.twig', [
                'commande' => null,
                'resultat' => $resultat,
                'classe' => $classe,
                'form' => $form->createView()
            ]);
        }
    }
}