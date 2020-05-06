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
use Symfony\Component\Form\FormError;
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
     * @Route("/add/{id}", name="add", requirements={"id"="\d+"})
     */
    public function add($id = 0, Request $request, EntityManagerInterface $entityManager)
    {
        $mod = '';
        if ($id == 0)
        {
            $mod='add';
            $trip = new Trip();
            $trip->setOrganizer($this->getUser());
            $trip->setCampus($this->getUser()->getCampus());
        }
        else
        {
            $mod='edit';
            $trip = $this->getDoctrine()->getRepository(Trip::class)->find($id);
            $state = $trip->getState()->getWording();
            $organizerId = $trip->getOrganizer()->getId();
            if ($state != 'en création' || $organizerId != $this->getUser()->getId())
            {
                $this->addFlash('danger','La sortie ne peut pas être modifiée');
                return $this->redirectToRoute('trip_dashboard', []);
            }
        }

        $tripForm = $this->createForm(TripType::class, $trip);
        $tripForm->handleRequest($request);

        if ($tripForm->isSubmitted())
        {
            if (!$trip->getLocation()) {
                $tripForm->get('location')->addError(
                    new FormError('Veuillez renseigner le lieu de la sortie.')
                );
            }
            else {
                if ($tripForm->isValid())
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
                    if ($state == 'add')
                    {
                        $this->addFlash('succes','La sortie est enregistrée');
                    }
                    elseif ($state = 'edit')
                    {
                        $this->addFlash('succes','La sortie est modifiée');
                    }

                    return $this->redirectToRoute('trip_dashboard', []);
                }
            }
        }

        return $this->render('trip/add.html.twig', [
            'tripForm' => $tripForm->createView(),
            'mod' => $mod,
        ]);
    }
}