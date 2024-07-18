<?php

namespace App\Repository;

use App\Entity\ProductAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<ProductAttribute>
 */
class ProductAttributeRepository extends ServiceEntityRepository
{
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, ProductAttribute::class);
        $this->logger = $logger;
    }

    public function findById(int $id): ?ProductAttribute
    {
        $productAttribute = $this->findOneBy(['id' => $id]);

        if ($productAttribute === null) {
            $message = sprintf('No Product Attribute with id %s', $id);
            $this->logger->error($message);
            throw new EntityNotFoundException($message);
        }

        return $productAttribute;
    }
}
