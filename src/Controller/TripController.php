<?php

namespace App\Controller;

use App\Entity\State;
use App\Data\SearchData;
use App\Entity\Trip;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\TripRepository;
use App\Form\TripType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function dashboard(TripRepository $repository, Request $request)
    {
        $tripRepo = $this->getDoctrine()->getRepository(Trip::class);
        $test = $tripRepo->findAll();
        $user = $this->getUser(User::class);
        $data = new SearchData();
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $tripsSearch = $repository->findSearch($data, $user);
        if(!empty($tripsSearch)) {
            $trips = $tripsSearch;
        } else {
            $trips = $this->addFlash('warning', 'Aucune sortie');
        }
        return $this->render('trip/dashboard.html.twig', [
            'trips' => $trips,
            'test' => $test,
            'form' => $form->createView(),
            'tripsSearch' => $tripsSearch,
        ]);
    }



    /**
     * Gère l'ajout d'une sortie
     * @Route("/add", name="add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager)
    {
        $trip = new Trip();
        $trip->setOrganizer($this->getUser());
        $trip->setCampus($this->getUser()->getCampus());

        $tripForm = $this->createForm(TripType::class, $trip);
        $tripForm->handleRequest($request);

        if ($tripForm->isSubmitted() && $tripForm->isValid())
        {
            $wording='';
            if ($tripForm->getClickedButton()->getName() == 'save')
            {
                $wording = 'en création';
            }
            elseif ($tripForm->getClickedButton()->getName() == 'publish')
            {
                $wording = 'ouvert';
            }
            $state = $this->getDoctrine()->getRepository(State::class)->findOneBy(['wording' => $wording]);
            $trip->setState($state);

            $entityManager->persist($trip);
            $entityManager->flush();
            $this->addFlash('succes','La sortie est enregistrée');
            return $this->redirectToRoute('trip_dashboard', []);
        }

        return $this->render('trip/add.html.twig', [
            'tripForm' => $tripForm->createView(),
        ]);
    }
}