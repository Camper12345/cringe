<?php

namespace App\Repository;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Random\Randomizer;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager,
    )
    {
        parent::__construct($registry, User::class);
    }

    public function findById(string|Uuid $id): ?User {
        if(is_string($id)) {
            $id = new Uuid($id);
        }

        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id->toBinary())
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByName(string $name): ?User {
        return $this->createQueryBuilder('u')
            ->andWhere('u.name = :val')
            ->setParameter('val', (string) $name)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByToken(string $token): ?User {
        return $this->createQueryBuilder('u')
            ->andWhere('u.token = :val')
            ->setParameter('val', (string) $token)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function save(User $user, bool $immediately = true): void {
        $this->entityManager->persist($user);
        if($immediately) {
            $this->entityManager->flush();
        }
    }
}
