<?php

namespace App\Repository;

use App\Entity\ItemAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<ItemAttribute>
 */
class ItemAttributeRepository extends ServiceEntityRepository
{
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, ItemAttribute::class);
        $this->logger = $logger;
    }

    public function findById(int $id): ?ItemAttribute
    {
        $attribute = $this->findOneBy(['id' => $id]);

        if ($attribute === null) {
            $message = sprintf('No Item Attribute with id %s', $id);
            $this->logger->error($message);
            throw new EntityNotFoundException($message);
        }

        return $attribute;
    }
}
