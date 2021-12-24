<?php

namespace App\Repository;

use App\Entity\Order;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @param array $params
     * @return Order|null
     */
    public function create(array $params): ?Order
    {
        try {
            $shippingDate = isset($params['shippingDate']) && $params['shippingDate'] != null ? new DateTime($params['shippingDate']) : null;
            $order = new Order();
            $order->setUserId($params['userId']);
            $order->setOrderCode($params['orderCode']);
            $order->setQuantity($params['quantity']);
            $order->setProductId($params['productId']);
            $order->setAddress($params['address']);
            $order->setShippingDate($shippingDate);
            $order->setCreatedAt(new DateTimeImmutable());
            $this->getEntityManager()->persist($order);
            $this->getEntityManager()->flush();
            return $order;
        } catch (Exception $e) {
            dd($e);
            return null;
        }
    }

    /**
     * @param int $orderId
     * @param array $params
     * @return bool
     */
    public function update(int $orderId, array $params): bool
    {
        try {
            $order = $this->find($orderId);
            if (!is_null($order)) {
                if (isset($params['orderCode']) && !empty($params['orderCode'])) {
                    $order->setOrderCode($params['orderCode']);
                }
                if (isset($params['productId']) && !empty($params['productId'])) {
                    $order->setProductId($params['productId']);
                }
                if (isset($params['quantity']) && !empty($params['quantity'])) {
                    $order->setQuantity($params['quantity']);
                }
                if (isset($params['address']) && !empty($params['address'])) {
                    $order->setAddress($params['address']);
                }
                if (isset($params['shippingDate']) && !empty($params['shippingDate'])) {
                    //dd($params['shippingDate']);
                    $order->setShippingDate(new DateTimeImmutable($params['shippingDate']));
                }

                $this->getEntityManager()->flush();
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
