<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuiController extends AbstractController
{

    /**
     * @Route("/gui", name="gui")
     */
    public function gui()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/gui.html.twig');
    }

    /**
     * @Route("/gui_overview", name="overview")
     */
    public function guiOverview()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/overview.html.twig');
    }

    /**
     * @Route("/gui_ticker", name="ticker")
     */
    public function guiTicker()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/ticker.html.twig');
    }

    /**
     * @Route("/gui_settings", name="settings")
     */
    public function guiSettings()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/settings.html.twig');
    }

    /**
     * @Route("/gui_users", name="users")
     */
     public function guiUsers()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');    
        return $this->render('gui/users.html.twig');
    } 

}