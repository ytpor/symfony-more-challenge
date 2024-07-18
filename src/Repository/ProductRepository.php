<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Product::class);
        $this->logger = $logger;
    }

    public function findById(int $id): ?Product
    {
        $product = $this->findOneBy(['id' => $id]);

        if ($product === null) {
            $message = sprintf('No Product with id %s', $id);
            $this->logger->error($message);
            throw new EntityNotFoundException($message);
        }

        return $product;
    }

    public function findByCategoryId(int $category_id): ?array
    {
        $product = $this->findBy(['category_id' => $category_id]);

        if (empty($product)) {
            $message = sprintf('No Product with category_id %s', $category_id);
            $this->logger->error($message);
            throw new EntityNotFoundException($message);
        }

        return $product;
    }

    public function findByIdJson(array $items): ?array
    {
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item['product_id'];
        }

        $product = $this->findBy(['id' => $ids]);

        if ($product === null) {
            $message = 'No Products found';
            $this->logger->error($message);
            throw new EntityNotFoundException($message);
        }

        return $product;
    }
}
