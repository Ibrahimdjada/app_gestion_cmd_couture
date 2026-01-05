<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommandeRepository;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Controller\BaseController;

class HomeController extends BaseController
{
    private $tokenManager;
    private $commandeRepository;

    public function __construct(CsrfTokenManagerInterface $tokenManager = null,CommandeRepository $commandeRepository)
    {
        $this->tokenManager = $tokenManager;
         $this->commandeRepository = $commandeRepository;
        
    }
    #[Route('/accueil', name: 'app_home')]
    public function index(UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {

        $users = $this->getUser();
        
        if (
            !$users 
        ) {
            return $this->redirectToRoute('app_login');
        }
            $mois = date('m').'-01';
            $debut = $mois;
            $fin = date('m-d');
            $user = $this->getUser();
            $roles = $user ? $user->getRoles() : [];
            $isTailleur = in_array('ROLE_TAILLEUR', $roles);

            if ($isTailleur) {
                $userId = $user->getId();
                // Afficher le total des commandes Non démarré (tous)
                $nombre = intval($this->commandeRepository->getCommandeMois('Non demarré', $debut, $fin)[0]['nombre'] ?? 0);
                // Les autres statuts sont filtrés par tailleur
                $nombre1 = intval($this->commandeRepository->getCommandeMoisTailleur('Encours', $debut, $fin, $userId)[0]['nombre'] ?? 0);
                $nombre2 = intval($this->commandeRepository->getCommandeMoisTailleur('Terminer', $debut, $fin, $userId)[0]['nombre'] ?? 0);
                $nombre3 = intval($this->commandeRepository->getCommandeMoisTailleur('Livrer', $debut, $fin, $userId)[0]['nombre'] ?? 0);
            } else {
                $nombre = intval($this->commandeRepository->getCommandeMois('Non demarré', $debut, $fin)[0]['nombre'] ?? 0);
                $nombre1 = intval($this->commandeRepository->getCommandeMois('Encours', $debut, $fin)[0]['nombre'] ?? 0);
                $nombre2 = intval($this->commandeRepository->getCommandeMois('Terminer', $debut, $fin)[0]['nombre'] ?? 0);
                $nombre3 = intval($this->commandeRepository->getCommandeMois('Livrer', $debut, $fin)[0]['nombre'] ?? 0);
            }
            return $this->render('dashboard.html.twig', [
                'controller_name' => 'HomeController',
                'response' => $nombre,
                'result1' => $nombre1,
                'result2' => $nombre2,
                'result3' => $nombre3
            ]);
   
    }

    
    // #[Route('/super_admin/dashboard', name: 'app_super_admin_dashboard')]
    // public function dashboard(): Response
    // {
       
    //         dd('connecter en tant que super admin');
    // }
    // #[Route('/user/dashboard', name: 'app_user_dashboard')]
    // public function userdashboard(): Response
    // {
       
    //         dd('connecter en tant que utilisateur');
    // }
}
