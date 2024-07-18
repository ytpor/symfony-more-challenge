<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Category::class);
        $this->logger = $logger;
    }

    public function findById(int $id): ?Category
    {
        $category = $this->findOneBy(['id' => $id]);

        if ($category === null) {
            $message = sprintf('No Category with id %s', $id);
            $this->logger->error($message);
            throw new EntityNotFoundException($message);
        }

        return $category;
    }
}
