<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GuiController extends AbstractController
{

    #[Route('/gui', name: 'gui')]
    public function gui(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/gui.html.twig');
    }

    #[Route('/gui_overview', name: 'overview')]
    public function guiOverview(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/overview.html.twig');
    }

    #[Route('/gui_ticker', name: 'ticker')]
    public function guiTicker(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/ticker.html.twig');
    }

    #[Route('/gui_settings', name: 'settings')]
    public function guiSettings(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/settings.html.twig');
    }

    #[Route('/gui_users', name: 'users')]
    public function guiUsers(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/users.html.twig');
    } 

}