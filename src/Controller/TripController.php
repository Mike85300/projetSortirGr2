<?php

namespace App\Controller;

use App\Entity\Trip;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TripController
 * @package App\Controller
 * @Route("/trip", name="trip_")
 */
class TripController extends AbstractController
{
    /**
     * @author : Romain Tanguy
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        $tripRepo = $this->getDoctrine()->getRepository(Trip::class);

        //TODO : filtre
        $trips = $tripRepo->findAll();

        return $this->render('trip/dashboard.html.twig', [
            'trips' => $trips,
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add()
    {
        return $this->render('trip/add.html.twig', []);
    }
}