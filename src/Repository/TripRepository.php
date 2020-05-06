<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Trip;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    /**
     * Récupère les sorties en lien avec une recherche
     * @author : Anthony MARTIN
     * @return Trip[] Un tableau de Trip
     */
    public function findSearch(SearchData $search, User $user): array
    {
        $query = $this
        ->createQueryBuilder('t')
        ->select('u', 't', 'e')
        ->join('t.organizer', 'u')
        ->join('t.state', 'e')
        ->andWhere('e.wording NOT LIKE :archive')
        ->setParameter(':archive', 'archivé');

        if(!empty($search->q)) {
            $query = $query
                ->having('t.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");

        }


       if(!empty($search->campus)) {
            $query = $query
                ->having('t.campus = :campus')
                ->setParameter('campus', $search->campus);

       }

        if(!empty($search->dateMin && $search->dateMax)) {
            $query = $query
                ->having('t.startDate BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $search->dateMin)
                ->setParameter('endDate', $search->dateMax);
        }

        if(!empty($search->userOrganisateur)) {
            $query = $query
                ->having('t.organizer = :user')
                ->setParameter('user', $user->getId());

        }

        if(!empty($search->userInscrit)) {
            $query = $query
                ->having('t.id IN (:participant)')
                ->andWhere('t.organizer = :user')
                ->setParameter('user', $user->getId())
                ->setParameter('participant', $user->getRegistredTrips());

        }

        if(!empty($search->userNonInscrit)) {
            $query = $query
                ->having('t.id NOT IN (:participant)')
                ->setParameter('participant', $user->getRegistredTrips());


        }

        if(!empty($search->finishedTrip)) {
            $query = $query
                ->having('e.wording = :termine')
                ->setParameter('termine', 'terminé');

        }

        if (empty($query)){
            $query = "Aucune sortie";

        }

        return $query->getQuery()->getResult();

    }


    /*
    public function findOneBySomeField($value): ?Trip
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
