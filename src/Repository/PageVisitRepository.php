<?php

namespace App\Repository;

use App\Entity\PageVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<PageVisit>
 */
class PageVisitRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager,
    )
    {
        parent::__construct($registry, PageVisit::class);
    }

    public function findOneById(string|Uuid $id): ?PageVisit {
        if(is_string($id)) {
            $id = new Uuid($id);
        }

        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $id->toBinary())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param integer $limit
     * @return array<PageVisit>
     */
    public function findRecent(int $limit, ?string $path = null): array {
        $builder = $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->orderBy('p.date', 'desc')
        ;

        if(null !== $path) {
            $builder
                ->setParameter('path', $path)
                ->andWhere('p.path = :path')
            ;
        }

        return $builder->getQuery()->getResult();
    }

    public function getVisitCount(?string $path = null): int {
        $builder = $this->createQueryBuilder('p')
            ->select('count(p.id)')
        ;

        if(null !== $path) {
            $builder
                ->setParameter('path', $path)
                ->andWhere('p.path = :path')
            ;
        }

        return $builder->getQuery()->getSingleScalarResult();
    }

    public function save(PageVisit $visit, bool $immediately = true): void {
        $this->entityManager->persist($visit);
        if($immediately) {
            $this->entityManager->flush();
        }
    }
}
