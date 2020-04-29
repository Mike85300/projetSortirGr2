<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Location;
use App\Entity\State;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        /**
         * State
         */
        $wordings = ['en création', 'ouvert', 'complet', 'fermé', 'annulé', 'en cours', 'terminé', 'archivé'];
        foreach ( $wordings as $wording)
        {
            $state = new State();
            $state->setWording($wording);
            $manager->persist($state);
        }



        /**
         * City
         */
        $cities = [
            'La Roche sur Yon' => '85000',
            'Saint-Herblain' => '44800',
            'Nantes' => '44000',
            'Chartres-de-Bretagne' => '35131',
            'Rennes' => '35000',
            'Quimper' => '29000',
            'Niort' => '79000',
            'Angers' => '49000',
            'Laval' => '53000',
            'La Mans' => '72000',
        ];

        foreach ($cities as $name => $zip)
        {
            $city = new City();
            $city->setName($name)->setZip($zip);
            $manager->persist($city);
        }



        /**
         * Campus
         */
        $campuses = [];

        $campusLRY = new Campus();
        $campusLRY->setName('La Roche-Sur-Yon');
        array_push($campuses, $campusLRY);

        foreach ($campuses as $campus)
        {
            $manager->persist($campus);
        }



        /**
         * User
         */
        $users = [];

        $bobMarley = new User();
        $bobMarley->setUsername('king_of_reggae')->setName('Marley')->setFirstname('Bob')->setEmail('bob.marley@gmail.com')->setPhone('0601020304')->setActive(false)->setCampus($campusLRY);
        $password = $this->encoder->encodePassword($bobMarley, 'azerty123');
        $bobMarley->setPassword($password);
        array_push($users, $bobMarley);

        foreach ($users as $user)
        {
            $manager->persist($user);
        }







        $manager->flush();
    }
}
