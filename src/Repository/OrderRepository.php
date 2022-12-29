<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Order::class);
    }

    public function add(Order $entity, bool $flush = false): void {

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void {

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Order[] Returns an array of Order objects
     */
    public function findOpenOrders(): ?array {

        return $this->createQueryBuilder('o')
                        ->addSelect('s')
                        ->join(OrderStatus::class, 's', Join::WITH, 'o.status = s.id')
                        ->andWhere('s.name = \'Open\' OR s.name = \'Ready\'')
                        ->orderBy('o.id', 'ASC')
                        ->getQuery()
                        ->getResult();
    }
}
