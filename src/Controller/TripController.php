<?php

namespace App\Controller;

use App\Entity\State;
use App\Entity\Trip;
use App\Entity\User;
use App\Form\TripType;
use App\Form\Type\CancelTripType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @author : Mickael Mandin
     * @Route("/register/{id}", name="register", requirements={"id"="\d+"})
     */
    public function tripRegister(EntityManagerInterface $em, UserInterface $user, $id)
    {
        $datenow = new \DateTime("now");
        $tripRepo = $this->getDoctrine()->getRepository(Trip::class);
        $trip = $tripRepo->find($id);
        $stateTrip = $trip->getState();
        $wordStateTrip = $stateTrip->getWording();
        /* add user */
        if ($user == $trip->getOrganizer()) {
            $this->addFlash('error', 'Vous ne pouvez pas vous inscrire, vous êtes l\'organisateur !');
        } elseif ($trip->getRegistrationDeadline() > $datenow) {
            if ($trip->getParticipants()->contains($user)) {
                $this->addFlash('error', 'Vous êtes déjà inscrit à cette sortie !');
            } elseif ($wordStateTrip === 'ouvert') {
                $trip->addParticipant($user);
                /* change state of trip */
                if (($trip->getRegistrationDeadline() <= $datenow) or (($trip->getMaxRegistrationNumber() != null) and ($trip->getMaxRegistrationNumber() == count($trip->getParticipants())))) {
                    $stateRepo = $this->getDoctrine()->getRepository(State::class);
                    $close = $stateRepo->findOneBy(['wording' => 'fermé']);
                    $trip->setState($close);
                }
                $this->addFlash('success', 'Votre inscription a bien été prise en compte !');
                $em->persist($trip);
                $em->flush();
            } else if ($wordStateTrip === 'en création') {
                $this->addFlash('error', 'Vous ne pouvez pas vous inscrire, les inscriptions ne sont pas commencées !');
            } elseif ($wordStateTrip === 'annulé') {
                $this->addFlash('error', 'Vous ne pouvez plus vous inscrire, la sortie est annulée!');
            } elseif ($wordStateTrip === 'fermé') {
                $this->addFlash('error', 'Désolé c\'est complet. Ressayez plus tard !');
            }
        } else {
            $this->addFlash('error', 'Vous ne pouvez plus vous inscrire, les inscriptions sont cloturées !');
        }

        return $this->redirectToRoute('trip_dashboard');
    }

    /**
     * @author : Mickael Mandin
     * @Route("/unsubscribe/{id}", name="unsubscribe", requirements={"id"="\d+"})
     */
    public function tripUnsubscribe(EntityManagerInterface $em, UserInterface $user, $id)
    {
        $datenow = new \DateTime("now");
        $tripRepo = $this->getDoctrine()->getRepository(Trip::class);
        $trip = $tripRepo->find($id);
        $stateTrip = $trip->getState();
        $wordStateTrip = $stateTrip->getWording();
        /* delete user */
        if($trip->getStartDate() > $datenow) {
            if($wordStateTrip === 'annulé'){
                $this->addFlash('error', 'La sortie a été annulée !' );
            }elseif ($trip->getParticipants()->contains($user)){
                $trip->removeParticipant($user);
                /* change state of trip*/
                if (($trip->getRegistrationDeadline() >= $datenow) and $wordStateTrip === 'fermé' and (($trip->getMaxRegistrationNumber() != null) and (($trip->getMaxRegistrationNumber()-1) == count($trip->getParticipants())))) {
                    $stateRepo = $this->getDoctrine()->getRepository(State::class);
                    $open = $stateRepo->findOneBy(['wording' => 'ouvert']);
                    $trip->setState($open);
                }
                $this->addFlash('success', 'Votre désinscription a bien été prise en compte !');
                $em->persist($trip);
                $em->flush();
            }else {
                $this->addFlash('error', 'Vous n\'êtes pas inscrit à cette sortie !' );
            }
        }elseif($wordStateTrip === 'en cours'){
            $this->addFlash('error', 'Trop tard ! La sortie a déjà débutée.');
        }elseif($wordStateTrip === 'terminé'){
            $this->addFlash('error', 'La sortie est terminée !');
        }
        return $this->redirectToRoute('trip_dashboard');
    }

    /**
     *@author : Mickael Mandin
     * @Route("/cancel/{id}", name="cancel", requirements={"id"="\d+"})
     */
    public function tripCancel(Request $request, EntityManagerInterface $em, UserInterface $user, $id)
    {
        $datenow = new \DateTime("now");
        $tripRepo = $this->getDoctrine()->getRepository(Trip::class);
        $trip = $tripRepo->find($id);
        $cancelTripForm = $this->createForm(CancelTripType::class);
        $stateTrip = $trip->getState();
        $wordStateTrip = $stateTrip->getWording();
        $cancelTripForm->handleRequest($request);

        if($wordStateTrip !== 'annulé') {
            if ($cancelTripForm->isSubmitted() && $cancelTripForm->isValid()) {
                if ($trip->getStartDate() > $datenow) {
                    if (($wordStateTrip === 'ouvert' or $wordStateTrip === 'fermé' or $wordStateTrip === 'en création') and $trip->getOrganizer() == $user) {
                        $info = $trip->getinformation();
                        $motif = $cancelTripForm->get("information")->getData();
                        $trip->setinformation('**ANNULEE** motif:'.$motif.' | '.$info);
                        $stateRepo = $this->getDoctrine()->getRepository(State::class);
                        $cancel = $stateRepo->findOneBy(['wording' => 'annulé']);
                        $trip->setState($cancel);
                        $this->addFlash('success', 'La sortie à bien été annulée !');
                        $em->persist($trip);
                        $em->flush();
                    } else {
                        $this->addFlash('error', 'Vous n\'êtes pas l\'organisateur de la sortie !');
                    }
                } elseif ($wordStateTrip === 'en cours') {
                    $this->addFlash('error', 'Vous ne pouvez pas annulée. La sortie a déjà commencée !');
                } else {
                    $this->addFlash('error', 'Vous ne pouvez pas annulée. La sortie est terminée !');
                }
                return $this->redirectToRoute('trip_dashboard');
            } else {
                return $this->render('trip/cancel.html.twig', [
                    "trip" => $trip, "cancelForm" => $cancelTripForm->createView()
                ]);
            }
        }else{
            $this->addFlash('error', 'La sortie a déjà été annulée !');
            return $this->redirectToRoute('trip_dashboard');
        }
    }
}