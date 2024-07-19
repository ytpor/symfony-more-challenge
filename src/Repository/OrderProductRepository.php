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

    public function findById(int $id): ?Order
    {
        $order = $this->findOneBy(['id' => $id]);

        if ($order === null) {
            throw new EntityNotFoundException(sprintf('No Order Product with id %s', $id));
        }

        return $order;
    }

    public function findByOrderProductId(int $id, int $product_id): ?OrderProduct
    {
        $product = $this->findOneBy(['order_id' => $id, 'product_id' => $product_id]);

        if ($product === null) {
            throw new EntityNotFoundException(sprintf('No Order Product with order_id %s', $id));
        }

        return $product;
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
