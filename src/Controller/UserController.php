<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Model\UserDto;
use App\Repository\UserRepository;
use App\Repository\UserProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /**
     * List all users.
     */
    #[Route('/api/user', methods: ['GET'])]
    #[OA\Tag(name: 'User')]
    #[Security(name: 'Bearer')]
    public function index(
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $user = $entityManager
            ->getRepository(User::class)
            ->findAll();

        return $this->json($user);
    }

    /**
     * Show a user.
     */
    #[Route('/api/user/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'User')]
    #[Security(name: 'Bearer')]
    public function show(
        UserRepository $userRepository,
        int $id
    ): JsonResponse
    {
        try {
            return $this->json($userRepository->findById($id));
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new user.
     */
    #[Route('/api/user', methods: ['POST'], format: 'json')]
    #[OA\Tag(name: 'User')]
    #[Security(name: 'Bearer')]
    public function create(
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] UserDto $userDto
    ): JsonResponse
    {
        try {
            $user = new User();
            $user->setEmail($userDto->email)
                ->setPassword($userDto->password);

            $entityManager->persist($user);
            $entityManager->flush();

            $profile = new UserProfile();
            $profile->setUserId($user->getId())
                ->setName($userDto->name)
                ->setPhone($userDto->phone)
                ->setAddress($userDto->address);

            $entityManager->persist($profile);
            $entityManager->flush();

            return $this->json([
                'id' => $user->getId(),
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a user.
     */
    #[Route('/api/user/{id}', methods: ['PUT'], format: 'json')]
    #[OA\Tag(name: 'User')]
    #[Security(name: 'Bearer')]
    public function update(
        UserProfileRepository $profileRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] UserDto $userDto,
        int $id
    ): JsonResponse
    {
        try {
            $user = $userRepository->findById($id);

            $user->setEmail($userDto->email)
                ->setPassword($userDto->password);

            $entityManager->persist($user);
            $entityManager->flush();

            $profile = $profileRepository->findByUserId($id);
            $profile->setName($userDto->name)
                ->setPhone($userDto->phone)
                ->setAddress($userDto->address);

            $entityManager->persist($profile);
            $entityManager->flush();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete a user.
     */
    #[Route('/api/user/{id}', methods: ['DELETE'])]
    #[OA\Tag(name: 'User')]
    #[Security(name: 'Bearer')]
    public function delete(
        UserProfileRepository $profileRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse
    {
        try {
            $profile = $profileRepository->findByUserId($id);
            $entityManager->remove($profile);
            $entityManager->flush();

            $user = $userRepository->findById($id);
            $entityManager->remove($user);
            $entityManager->flush();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
