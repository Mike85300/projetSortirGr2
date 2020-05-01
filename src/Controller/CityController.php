<?php

namespace App\Controller;

use App\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/city", name="city_")
 */
class CityController extends AbstractController
{
    /**
     * @author Romain Tanguy
     * Retourne un tableau de villes aux format JSON dont le nom ou le zip commence par le pattern passé en paramètre de la requête
     * @Route("/ajax", name="ajax")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response|JsonResponse
     */
    public function ajax(Request $request, SerializerInterface $serializer)
    {
        if ($request->isXmlHttpRequest())
        {
            $pattern = $request->get('pattern');
            $cityRepo = $this->getDoctrine()->getRepository(City::class);
            $cities = $cityRepo->findByPattern($pattern);
            $serialized = $serializer->serialize($cities,'json');
            return new JsonResponse($serialized);
        }
        return new Response("ERROR !",400);
    }
}
