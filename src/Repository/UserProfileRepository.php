<?php

namespace App\Repository;

use App\Entity\UserProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @extends ServiceEntityRepository<UserProfile>
 */
class UserProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProfile::class);
    }

    public function findById(int $id): ?UserProfile
    {
        $profile = $this->findOneBy(['id' => $id]);

        if ($profile === null) {
            throw new EntityNotFoundException(sprintf('No User Profile with id %s', $id));
        }

        return $profile;
    }

    public function findByUserId(int $id): ?UserProfile
    {
        $profile = $this->findOneBy(['user_id' => $id]);

        if ($profile === null) {
            throw new EntityNotFoundException(sprintf('No User Profile with user_id %s', $id));
        }

        return $profile;
    }
}
