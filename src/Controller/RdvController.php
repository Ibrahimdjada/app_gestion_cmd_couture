<?php

namespace App\Controller;

use App\Entity\Rdv;
use App\Constante\Constantes;
use App\Form\RdvType;
use App\Form\RdvEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class RdvController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/rdv', name: 'list_rdv')]
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
        $Rdvs = $this->entityManager->getRepository(Rdv::class)->findAll();

        return $this->render('Rdv/IndexRDV.html.twig', [
            'Rdvs' => $Rdvs,
        ]);
    }
    #[Route('/rdv/{id}/delete', name: 'delete_rdv')]

    public function delete(Rdv $rdv): Response
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
        $this->entityManager->remove($rdv);
        $this->entityManager->flush();

        // Ajouter un flash message de suppression réussie
        $this->addFlash('success', 'Le rdv a été supprimé avec succès.');

        // Redirection vers la liste des rdvs après la suppression
        return $this->redirectToRoute('list_rdv');
    }
}
