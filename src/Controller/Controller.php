<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function index()
    {
        return $this->render('/accueil.html.twig');
    }
}
