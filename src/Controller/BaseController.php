<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BaseController extends AbstractController
{
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        if (
            !$this->isGranted('IS_AUTHENTICATED_FULLY')
        ) {
            // return $this->redirectToRoute('app_login');
        header('Location: '.$urlGenerator->generate('app_login'));
        exit;
            // throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
    }
}
