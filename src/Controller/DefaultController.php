<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/layout/", name="default_layout")
     */
    public function layout()
    {

        return $this->render('layout.html.twig');
    }




}
