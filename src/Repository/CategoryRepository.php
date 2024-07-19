<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    private $cache;
    private $logger;

    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger,
        CacheInterface $cache
    )
    {
        parent::__construct($registry, Category::class);
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function findAllBy(): array
    {
        return $this->cache->get('category_all', function (ItemInterface $item) {
            $item->expiresAfter(3600); // Cache for 1 hour

            return $this->findBy([]);
        });
    }

    public function findById(int $id): ?Category
    {
        $product = $this->findOneBy(['id' => $id]);

        if ($product === null) {
            $message = sprintf('No Category with id %s', $id);
            $this->logger->error($message);
            throw new EntityNotFoundException($message);
        }

        return $product;
    }

    public function clearCachedData(): void
    {
        $this->cache->delete('category_all');
    }
}
