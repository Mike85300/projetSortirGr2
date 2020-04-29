<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Location;
use App\Entity\State;
use App\Entity\Trip;
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
        $states = [];

        $inCreation = new State();
        $inCreation->setWording('en création');
        array_push($states, $inCreation);

        $open = new State();
        $open->setWording('ouvert');
        array_push($states, $open);

        $full = new State();
        $full->setWording('complet');
        array_push($states, $full);

        $close = new State();
        $close->setWording('fermé');
        array_push($states, $close);

        $canceled = new State();
        $canceled->setWording('annulé');
        array_push($states, $canceled);

        $inProgress = new State();
        $inProgress->setWording('en cours');
        array_push($states, $inProgress);

        $over = new State();
        $over->setWording('terminé');
        array_push($states, $over);

        $archived = new State();
        $archived->setWording('archivé');
        array_push($states, $archived);

        foreach ($states as $state)
        {
            $manager->persist($state);
        }



        /**
         * City
         */
        $cities = [];

        $laRocheSurYon = new City();
        $laRocheSurYon->setName('La Roche-sur-Yon')->setZip('85000');
        array_push($cities, $laRocheSurYon);

        $saintHerblain = new City();
        $saintHerblain->setName('Saint-Herblain')->setZip('44800');
        array_push($cities, $saintHerblain);

        $nantes = new City();
        $nantes->setName('Nantes')->setZip('44000');
        array_push($cities, $nantes);

        $chartresDeBretagne = new City();
        $chartresDeBretagne->setName('Chartres-de-Bretagne')->setZip('35131');
        array_push($cities, $chartresDeBretagne);

        $rennes = new City();
        $rennes->setName('Rennes')->setZip('3500');
        array_push($cities, $rennes);

        $quimper = new City();
        $quimper->setName('Quimper')->setZip('29000');
        array_push($cities, $quimper);

        $Niort = new City();
        $Niort->setName('Niort')->setZip('79000');
        array_push($cities, $Niort);

        $angers = new City();
        $angers->setName('Angers')->setZip('49000');
        array_push($cities, $angers);

        $laval = new City();
        $laval->setName('Laval')->setZip('53000');
        array_push($cities, $laval);

        $leMans = new City();
        $leMans->setName('Le Mans')->setZip('72000');
        array_push($cities, $leMans);

        foreach ($cities as $city)
        {
            $manager->persist($city);
        }



        /**
         * Campus
         */
        $campuses = [];

        $campusLRY = new Campus();
        $campusLRY->setName('La Roche-Sur-Yon');
        array_push($campuses, $campusLRY);

        $campusNiort = new Campus();
        $campusNiort->setName('Niort');
        array_push($campuses, $campusNiort);

        $campusNantes = new Campus();
        $campusNantes->setName('Nantes Faraday');
        array_push($campuses, $campusNantes);

        $campusAngers = new Campus();
        $campusAngers->setName('Angers');
        array_push($campuses, $campusAngers);

        $campusLeMans = new Campus();
        $campusLeMans->setName('La Mans');
        array_push($campuses, $campusLeMans);

        $campusLaval = new Campus();
        $campusLaval->setName('Laval');
        array_push($campuses, $campusLaval);

        $campusRennes = new Campus();
        $campusRennes->setName('Rennes');
        array_push($campuses, $campusRennes);

        $campusQuimper = new Campus();
        $campusQuimper->setName('Quimper');
        array_push($campuses, $campusQuimper);

        foreach ($campuses as $campus)
        {
            $manager->persist($campus);
        }



        /**
         * Location
         */
        $locations = [];

        $afpaLRY = new Location();
        $afpaLRY->setName('AFPA')->setStreet('12 Impasse Ampère')->setCity($laRocheSurYon)->setLatitude(46.660587310791016)->setLongitude(-1.4399538040161133);
        array_push($locations, $afpaLRY);

        $placeNapoleonLRY = new Location();
        $placeNapoleonLRY->setName('Place Napoléon')->setStreet('Place Napoléon')->setCity($laRocheSurYon)->setLatitude(46.670597076416016)->setLongitude(-1.4266810417175293);
        array_push($locations, $placeNapoleonLRY);

        $leGrandCafeLRY = new Location();
        $leGrandCafeLRY->setName('Le Grand Café')->setStreet('4 rue Georges Clemenceau')->setCity($laRocheSurYon)->setLatitude(46.6702025)->setLongitude(-1.4285465);
        array_push($locations, $leGrandCafeLRY);

        $vbLRY = new Location();
        $vbLRY->setName('V&B La Roche Sud')->setStreet('10 Rue Henri Aucher')->setCity($laRocheSurYon)->setLatitude(46.6574328)->setLongitude(-1.4422799);
        array_push($locations, $vbLRY);

        $piscineAragoLRY = new Location();
        $piscineAragoLRY->setName('Piscine complexe Arago')->setStreet('Impasse des Olympiades')->setCity($laRocheSurYon)->setLatitude(46.669096)->setLongitude(-1.446366);
        array_push($locations, $piscineAragoLRY);

        $cinevilleLRY = new Location();
        $cinevilleLRY->setName('Cineville')->setStreet('Rue François Cevert')->setCity($laRocheSurYon)->setLatitude(46.6979866027832)->setLongitude(-1.4270051717758179);
        array_push($locations, $cinevilleLRY);

        foreach ($locations as $location)
        {
            $manager->persist($location);
        }



        /**
         * User
         */
        $users = [];

        $bobMarley = new User();
        $bobMarley->setUsername('king_of_reggae')->setName('Marley')->setFirstname('Bob')->setEmail('bob.marley@gmail.com')->setPhone('0601020304')->setActive(false)->setCampus($campusLRY)->setRoles(['ROLE_USER']);
        $password = $this->encoder->encodePassword($bobMarley, 'azerty123');
        $bobMarley->setPassword($password);
        array_push($users, $bobMarley);

        $anthonyMartin = new User();
        $anthonyMartin->setUsername('amartin')->setName('Martin')->setFirstname('Anthony')->setEmail('anthony.martin2019@campus-eni.fr')->setPhone('0611111111')->setActive(true)->setCampus($campusLRY)->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $password = $this->encoder->encodePassword($anthonyMartin, 'azerty123');
        $anthonyMartin->setPassword($password);
        array_push($users, $anthonyMartin);

        $mickaelMandin = new User();
        $mickaelMandin->setUsername('mmandin')->setName('Mandin')->setFirstname('Mickael')->setEmail('mickael.mandin2019@campus-eni.fr')->setPhone('0622222222')->setActive(true)->setCampus($campusLRY)->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $password = $this->encoder->encodePassword($mickaelMandin, 'azerty123');
        $mickaelMandin->setPassword($password);
        array_push($users, $mickaelMandin);

        $romaintanguy = new User();
        $romaintanguy->setUsername('rtanguy')->setName('Tanguy')->setFirstname('Romain')->setEmail('romain.tanguy2019@campus-eni.fr')->setPhone('0633333333')->setActive(true)->setCampus($campusLRY)->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $password = $this->encoder->encodePassword($romaintanguy, 'azerty123');
        $romaintanguy->setPassword($password);
        array_push($users, $romaintanguy);

        foreach ($users as $user)
        {
            $manager->persist($user);
        }



        /**
         * Trip
         */
        $trips = [];

        $atelierPhilo1 = new Trip();
        $atelierPhilo1
            ->setName('Atelier philo #1')
            ->setCampus($campusLRY)
            ->setLocation($leGrandCafeLRY)
            ->setOrganizer($bobMarley)
            ->setStartDate(date_create('2020-03-07 18:00'))
            ->setDuration(120)
            ->setRegistrationDeadline(date_create('2020-03-06 18:00'))
            ->setMaxRegistrationNumber(8)
            ->setinformation('Thème de la soirée : La morale est-elle la meilleure des politiques ?')
            ->setState($archived)
            ->addParticipant($bobMarley)->addParticipant($anthonyMartin)->addParticipant($mickaelMandin)->addParticipant($romaintanguy);
        array_push($trips, $atelierPhilo1);

        $atelierPhilo2 = new Trip();
        $atelierPhilo2
            ->setName('Atelier philo #2')
            ->setCampus($campusLRY)
            ->setLocation($leGrandCafeLRY)
            ->setOrganizer($bobMarley)
            ->setStartDate(date_create('2020-04-04 18:00'))
            ->setDuration(120)
            ->setRegistrationDeadline(date_create('2020-04-03 18:00'))
            ->setMaxRegistrationNumber(8)
            ->setinformation('**ANNULE** Thème de la soirée : La pluralité des cultures fait-elle obstacle à l’unité du genre humain ?')
            ->setState($canceled)
            ->addParticipant($bobMarley)->addParticipant($anthonyMartin)->addParticipant($mickaelMandin)->addParticipant($romaintanguy);
        array_push($trips, $atelierPhilo2);

        $atelierPhilo3 = new Trip();
        $atelierPhilo3
            ->setName('Atelier philo #3')
            ->setCampus($campusLRY)
            ->setLocation($leGrandCafeLRY)
            ->setOrganizer($bobMarley)
            ->setStartDate(date_create('2020-07-04 18:00'))
            ->setDuration(120)
            ->setRegistrationDeadline(date_create('2020-07-03 18:00'))
            ->setMaxRegistrationNumber(8)
            ->setinformation('Thème de la soirée : Est-il possible d\'échapper au temps ?')
            ->setState($inCreation);
        array_push($trips, $atelierPhilo3);

        $cinema1 = new Trip();
        $cinema1
            ->setName('Soirée Ciné !')
            ->setCampus($campusLRY)
            ->setLocation($cinevilleLRY)
            ->setOrganizer($bobMarley)
            ->setStartDate(date_create('2020-08-15 20:00'))
            ->setDuration(120)
            ->setRegistrationDeadline(date_create('2020-08-15 19:00'))
            ->setMaxRegistrationNumber(null)
            ->setinformation('Film à prévoir')
            ->setState($open)
            ->addParticipant($bobMarley)->addParticipant($anthonyMartin)->addParticipant($mickaelMandin)->addParticipant($romaintanguy);
        array_push($trips, $cinema1);

        $echec1 = new Trip();
        $echec1
            ->setName('Partie d\'echec')
            ->setCampus($campusLRY)
            ->setLocation($afpaLRY)
            ->setOrganizer($bobMarley)
            ->setStartDate(date_create('2020-05-15 12:30'))
            ->setDuration(120)
            ->setRegistrationDeadline(date_create('2020-05-15 12:00'))
            ->setMaxRegistrationNumber(2)
            ->setinformation('')
            ->setState($full)
            ->addParticipant($bobMarley)->addParticipant($anthonyMartin);
        array_push($trips, $echec1);

        $piscine1 = new Trip();
        $piscine1
            ->setName('Après-midi piscine !')
            ->setCampus($campusLRY)
            ->setLocation($piscineAragoLRY)
            ->setOrganizer($anthonyMartin)
            ->setStartDate(date_create('2020-07-11 14:00'))
            ->setDuration(120)
            ->setRegistrationDeadline(date_create('2020-07-10 14:00'))
            ->setMaxRegistrationNumber(12)
            ->setinformation('')
            ->setState($open)
            ->addParticipant($anthonyMartin)->addParticipant($mickaelMandin)->addParticipant($romaintanguy);
        array_push($trips, $piscine1);

        foreach ($trips as $trip)
        {
            $manager->persist($trip);
        }

        $projet = new Trip();
        $projet
            ->setName('Projet 2')
            ->setCampus($campusLRY)
            ->setLocation($afpaLRY)
            ->setOrganizer($bobMarley)
            ->setStartDate(date_create('2020-04-27 09:00'))
            ->setDuration(120)
            ->setRegistrationDeadline(date_create('2020-04-27 08:00'))
            ->setMaxRegistrationNumber(3)
            ->setinformation('Réalisation de l\'application web sortir.com avec PHP (Symfony) et MySql')
            ->setState($inProgress)
            ->addParticipant($anthonyMartin)->addParticipant($mickaelMandin)->addParticipant($romaintanguy);
        array_push($trips, $projet);

        $yoga1 = new Trip();
        $yoga1
            ->setName('Yoga en plein air')
            ->setCampus($campusLRY)
            ->setLocation($placeNapoleonLRY)
            ->setOrganizer($mickaelMandin)
            ->setStartDate(date_create('2020-06-20 14:00'))
            ->setDuration(120)
            ->setRegistrationDeadline(date_create('2020-04-19 14:00'))
            ->setMaxRegistrationNumber(null)
            ->setinformation('Séance ouverte à tous pour découvrir le yoga en plein air en centre ville.')
            ->setState($close)
            ->addParticipant($anthonyMartin)->addParticipant($mickaelMandin)->addParticipant($romaintanguy);
        array_push($trips, $yoga1);

        $coronapero1 = new Trip();
        $coronapero1
            ->setName('CoronApéro !')
            ->setCampus($campusLRY)
            ->setLocation($vbLRY)
            ->setOrganizer($bobMarley)
            ->setStartDate(date_create('2020-04-25 19:00'))
            ->setDuration(180)
            ->setRegistrationDeadline(date_create('2020-04-25 19:00'))
            ->setMaxRegistrationNumber(null)
            ->setinformation('Apéro à distance en organisé avec V&B La Roche Sud')
            ->setState($over)
            ->addParticipant($bobMarley)->addParticipant($anthonyMartin)->addParticipant($mickaelMandin)->addParticipant($romaintanguy);
        array_push($trips, $coronapero1);

        $coronapero2 = new Trip();
        $coronapero2
            ->setName('CoronApéro !')
            ->setCampus($campusLRY)
            ->setLocation($vbLRY)
            ->setOrganizer($bobMarley)
            ->setStartDate(date_create('2020-05-16 19:00'))
            ->setDuration(180)
            ->setRegistrationDeadline(date_create('2020-05-15 19:00'))
            ->setMaxRegistrationNumber(null)
            ->setinformation('Apéro à distance en organisé avec V&B La Roche Sud')
            ->setState($open)
            ->addParticipant($bobMarley)->addParticipant($anthonyMartin)->addParticipant($mickaelMandin)->addParticipant($romaintanguy);
        array_push($trips, $coronapero2);

        foreach ($trips as $trip)
        {
            $manager->persist($trip);
        }


        $manager->flush();
    }
}
