<?php

namespace App\Form\DataTransformer;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Romain Tanguy
 * Class CityToStringTransformer
 * @package App\Form\DataTransformer
 */
class CityToStringTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (city) to a string (name).
     * @param City|null $city
     * @return string
     */
    public function transform($city)
    {
        if ($city === null)
        {
            return '';
        }

        return $city->getName();
    }

    /**
     * Transforms a string ( name (zip) ) to an object (city)
     * @param mixed $token
     * @return mixed|object[]|void
     */
    public function reverseTransform($token)
    {
        $city = null;

        if (!$token)
        {
            return;
        }

        $pattern = '#^\D+\s\(\d{5}\)$#';
        preg_match($pattern, $token, $matches);
        if ($matches)
        {
            $str = explode(' (', $token);
            $name = $str[0];
            $zip = explode(')', $str[1])[0];
            $city = $this->entityManager->getRepository(City::class)->findOneBy(['name' => $name, 'zip' => $zip]);
        }

        if ($city === null)
        {
            // causes a validation error
            // this message is not shown to the user
            throw new TransformationFailedException( sprintf(
                'An issue with city "%s" does not exist!',
                $token
            ));
        }

        return $city;

    }
}