<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MailViewerController extends AbstractController
{
    #[Route('/mail/base', name: 'app_mail_viewer')]
    public function index(): Response
    {
        return $this->render('email/base.html.twig', [
            'controller_name' => 'MailViewerController',
        ]);
    }
}
