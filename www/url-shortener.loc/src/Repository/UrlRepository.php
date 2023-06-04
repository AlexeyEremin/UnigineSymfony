<?php

namespace App\Repository;

use App\Entity\Url;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Url|null find($id, $lockMode = null, $lockVersion = null)
 * @method Url|null findOneBy(array $criteria, array $orderBy = null)
 * @method Url[]    findAll()
 * @method Url[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Url::class);
    }

    public function findOneByHash(string $value): ?Url
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.hash = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBetweenUniqueUrl($start, $end)
    {
        $bq = $this->getEntityManager();

        return count(
            $this->createQueryBuilder('u')
                ->select('u.url')
                ->add(
                    'where',
                    $bq->getExpressionBuilder()
                        ->between('u.createdDate', ':start', ':end')
                )
                ->setParameter("start", $start)
                ->setParameter("end", $end)
                ->distinct()
                ->getQuery()
                ->getArrayResult()
        );
    }

    public function findUniqueUrl(string $url): int
    {
        $uq = $this->createQueryBuilder('u');

        return count(
            $this->createQueryBuilder('u')
                ->select('u.url')
                ->distinct()
                ->add(
                    'where',
                    $uq->expr()->like('u.url', ':url')
                )
                ->setParameter('url', "%{$url}%")
                ->getQuery()
                ->getArrayResult()
        );
    }
}
