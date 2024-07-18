<?php

namespace App\Repository;

use App\Entity\Order;
use App\Repository\OrderProductRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger,
        OrderProductRepository $productRepository
    )
    {
        parent::__construct($registry, Order::class);
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    public function findById(int $id): ?Order
    {
        $order = $this->findOneBy(['id' => $id]);

        if ($order === null) {
            throw new EntityNotFoundException(sprintf('No Order with id %s', $id));
        }

        return $order;
    }
}
