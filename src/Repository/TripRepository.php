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
     * @return Trip[] Un tableau de Trip
     * @author : Anthony MARTIN
     */
    public function findSearch(SearchData $search, User $user): array
    {
        $tab = [];
        $andWhere = '';
        $lgTab = 0;

        $query = $this
            ->createQueryBuilder('t')
            ->select('u', 't', 'e')
            ->join('t.organizer', 'u')
            ->join('t.state', 'e')
            ->andWhere('e.wording NOT LIKE :archive')
            ->setParameter(':archive', 'archivé');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('t.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");

        }


        if (!empty($search->campus)) {
            $query = $query
                ->andWhere('t.campus = :campus')
                ->setParameter('campus', $search->campus);

        }

        if (!empty($search->dateMin && $search->dateMax)) {
            $query = $query
                ->andWhere('t.startDate BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $search->dateMin)
                ->setParameter('endDate', $search->dateMax);
        }
///////////////////////////////////////////

        if (!empty($search->userOrganisateur)) {
            $tab['userOrga'] = 't.organizer = :user';
            $query = $query
                ->setParameter('user', $user->getId());
        }


        //////////////////////////////////////////////////////////
        if (!empty($search->userInscrit)) {
                $tab['userInscrit'] = 't.id IN (:participant)';
            $query = $query
                ->setParameter('participant', $user->getRegistredTrips());

        }

        ////////////////////////////////////////////////////////////////

        if (!empty($search->userNonInscrit)) {
            $tab['userNonInscrit'] = 't.id NOT IN (:participant)';
            $query = $query
                ->setParameter('participant', $user->getRegistredTrips());
         }
        /////////////////////////////////////////////////////////////

        if (!empty($search->finishedTrip)) {
            $tab['finishedTrip'] = 'e.wording = :termine';
            $query = $query
                ->setParameter('termine', 'terminé');
            }

//////////////////////////////////////////////

        if (empty($query)) {
            $query = "Aucune sortie";

        }

        if ($tab != null){
            $lgTab = count($tab);
            $cptr = 1;

            foreach ($tab as $valeur){
                $andWhere = $andWhere.$valeur;
                if ($cptr != $lgTab){
                    $andWhere = $andWhere.' OR ';
                    $cptr++;
                }
            }
            $query = $query
                ->andWhere($andWhere);
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
