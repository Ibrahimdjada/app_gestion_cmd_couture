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

        // if (
        //     !$this->isGranted('IS_AUTHENTICATED_FULLY')
        // ) {
        //     return $this->redirectToRoute('app_login');
        // }
            $mois = date('m').'-01';
            $debut =$mois;
            $fin = date('m-d');
            $result = $this->commandeRepository-> getCommandeMois('Non demarré',$debut,$fin);
                $nombre = intval($result[0]['nombre']);
                $result1 = $this->commandeRepository-> getCommandeMois('Encours',$debut,$fin);
                $nombre1 = intval($result1[0]['nombre']);
                $result2 = $this->commandeRepository-> getCommandeMois('Terminer',$debut,$fin);
                $nombre2 = intval($result2[0]['nombre']);
                $result3 = $this->commandeRepository-> getCommandeMois('Livrer',$debut,$fin);
                $nombre3 = intval($result3[0]['nombre']);
            return $this->render('dashboard.html.twig', [
                'controller_name' => 'HomeController','response' => $nombre,'result1'=>$nombre1,'result2'=>$nombre2,'result3'=>$nombre3
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
