<?php

namespace App\Repository;

use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscription>
 *
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    public function save(Inscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Inscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Inscription[] Returns an array of Inscription objects
//     */
    public function estInscrit(int $UserId, int $sortieId): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.utilisateur = :idUser')
            ->setParameter('idUser', $UserId)
            ->andWhere('i.sortie = :idSortie')
            ->setParameter('idSortie', $sortieId)
            ->getQuery()
            ->getResult()
        ;
    }
//       $queryBuilder=$inscriptionRepository->createQueryBuilder('i');
//        $query=$queryBuilder->select('i.id')
//            ->where('i.utilisateur = :idUser')
//            ->setParameter('idUser', $userId)
//            ->getQuery()
//            ->getResult();
//    public function findOneBySomeField($value): ?Inscription
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
