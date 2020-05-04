<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Location;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/location", name="location_")
 */
class LocationController extends AbstractController
{

    /**
     * @author Romain Tanguy
     * Retourne un tableau d'objet Location, au format JSON, qui sont dans la ville dont le nom et le code postal sont passé en paramètre de la requête
     * @Route("/ajax", name="ajax")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse|Response
     */
    public function ajax(Request $request, SerializerInterface $serializer)
    {
        if ($request->isXmlHttpRequest())
        {
            $cityName = $request->get('city');
            $cityZip = $request->get('zip');

            //requête en DB pour récupérer la ville (son id) :
            $city = $this->getDoctrine()->getRepository(City::class)->findOneBy(['name' => $cityName, 'zip' => $cityZip]);

            //requête en DB pour récupérer la liste des locations qui sont dans la ville
            $locationRepo = $this->getDoctrine()->getRepository(Location::class);
            $locations = $locationRepo->findBy(['city' => $city->getId()]);

            $serialized = $serializer->serialize($locations,'json');
            return new JsonResponse($serialized);
        }
        return new Response("ERROR !",400);
    }

    /**
     * @author Romain Tanguy
     * Retourne un objet Location, au format JSON (recherche avec son id)
     * @Route("/ajax2", name="ajax2")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse|Response
     */
    public function ajax2(Request $request, SerializerInterface $serializer)
    {
        if ($request->isXmlHttpRequest())
        {
            $locationId = $request->get('locationId');
            $locationRepo = $this->getDoctrine()->getRepository(Location::class);
            $location = $locationRepo->find($locationId);
            $serialized = $serializer->serialize($location,'json');
            return new JsonResponse($serialized);
        }
        return new Response("ERROR !",400);
    }
}
