<?php

namespace App\Controller;

use App\Entity\State;
use App\Data\SearchData;
use App\Entity\Trip;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\TripRepository;
use App\Form\TripType;
use App\Form\Type\CancelTripType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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

    /**
     * @author : Mickael Mandin
     * @Route("/register/{id}", name="register", requirements={"id"="\d+"})
     */
    public function tripRegister(EntityManagerInterface $em, UserInterface $user, $id)
    {
        $datenow = new \DateTime("now");
        $tripRepo = $this->getDoctrine()->getRepository(Trip::class);
        $trip = $tripRepo->find($id);
        if(!empty($trip)) {
            $stateTrip = $trip->getState();
            $wordStateTrip = $stateTrip->getWording();
            /* add user */
            if ($user == $trip->getOrganizer()) {
                $this->addFlash('danger', 'Vous ne pouvez pas vous inscrire, vous êtes l\'organisateur !');
            } elseif ($trip->getRegistrationDeadline() > $datenow) {
                if ($trip->getParticipants()->contains($user)) {
                    $this->addFlash('danger', 'Vous êtes déjà inscrit à cette sortie !');
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
                    $this->addFlash('danger', 'Vous ne pouvez pas vous inscrire, les inscriptions ne sont pas commencées !');
                } elseif ($wordStateTrip === 'annulé') {
                    $this->addFlash('danger', 'Vous ne pouvez plus vous inscrire, la sortie est annulée!');
                } elseif ($wordStateTrip === 'fermé') {
                    $this->addFlash('danger', 'Désolé c\'est complet. Ressayez plus tard !');
                }
            } else {
                $this->addFlash('danger', 'Vous ne pouvez plus vous inscrire, les inscriptions sont cloturées !');
            }
        } else {
            $this->addFlash('danger', 'La sortie n\'existe pas !');
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
        if(!empty($trip)) {
            $stateTrip = $trip->getState();
            $wordStateTrip = $stateTrip->getWording();
            /* delete user */
            if($trip->getStartDate() > $datenow) {
                if($wordStateTrip === 'annulé'){
                    $this->addFlash('danger', 'La sortie a été annulée !' );
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
                    $this->addFlash('danger', 'Vous n\'êtes pas inscrit à cette sortie !' );
                }
            }elseif($wordStateTrip === 'en cours'){
                $this->addFlash('danger', 'Trop tard ! La sortie a déjà débutée.');
            }elseif($wordStateTrip === 'terminé'){
                $this->addFlash('danger', 'La sortie est terminée !');
            }
        } else {
            $this->addFlash('danger', 'La sortie n\'existe pas !');
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
        if(!empty($trip)) {
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
                            $this->addFlash('danger', 'Vous n\'êtes pas l\'organisateur de la sortie !');
                        }
                    } elseif ($wordStateTrip === 'en cours') {
                        $this->addFlash('danger', 'Vous ne pouvez pas annulée. La sortie a déjà commencée !');
                    } else {
                        $this->addFlash('danger', 'Vous ne pouvez pas annulée. La sortie est terminée !');
                    }
                    return $this->redirectToRoute('trip_dashboard');
                } else {
                    return $this->render('trip/cancel.html.twig', [
                        "trip" => $trip, "cancelForm" => $cancelTripForm->createView()
                    ]);
                }
            }else{
                $this->addFlash('danger', 'La sortie a déjà été annulée !');
                return $this->redirectToRoute('trip_dashboard');
            }
        }else{
            $this->addFlash('danger', 'La sortie n\'existe pas !');
            return $this->redirectToRoute('trip_dashboard');
        }
    }
}