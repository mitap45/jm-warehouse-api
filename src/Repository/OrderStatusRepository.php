<?php
/**
 * Created by PhpStorm.
 * User: mitap
 * Date: 24.12.2018
 * Time: 12:49
 */

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


class OrderStatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderStatus::class);
    }

    /**
     * Getting latest current status of the order by orderId
     * @param int $orderId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    function getOrderStatusByOrderId(int $orderId)
    {
        $qb = $this->createQueryBuilder("os");
        $qb
            ->select('os')
            ->where('os.order = :orderId')
            ->setParameter('orderId', $orderId)
            ->orderBy('os.createDate', 'DESC')
            ->setMaxResults(1);
        ;
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;

    }

}