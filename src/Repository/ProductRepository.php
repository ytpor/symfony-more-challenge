<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    private $cache;
    private $logger;

    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger,
        CacheInterface $cache
    )
    {
        parent::__construct($registry, Product::class);
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function findAllBy(): array
    {
        return $this->cache->get('product_all', function (ItemInterface $item) {
            $item->expiresAfter(3600); // Cache for 1 hour

            return $this->findBy([]);
        });
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

    public function clearCachedData(): void
    {
        $this->cache->delete('product_all');
    }

}
