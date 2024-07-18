<?php

namespace App\Repository;

use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @extends ServiceEntityRepository<OrderProduct>
 */
class OrderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    public function findByOrderId(int $id): ?array
    {
        $product = $this->findBy(['order_id' => $id]);

        if ($product === null) {
            throw new EntityNotFoundException(sprintf('No Order Product with order_id %s', $id));
        }

        return $product;
    }
}
