<?php

namespace App\Controller;

use App\Entity\Category;
use App\Model\CategoryDto;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController  extends BaseController
{
    /**
     * List all categories.
     */
    #[Route('/api/category', methods: ['GET'])]
    #[OA\Tag(name: 'Category')]
    #[Security(name: 'Bearer')]
    public function index(
        CategoryRepository $categoryRepository
    ): JsonResponse
    {
        $category = $categoryRepository->findAllBy();

        return $this->json($category);
    }

    /**
     * Show a category.
     */
    #[Route('/api/category/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'Category')]
    #[Security(name: 'Bearer')]
    public function show(
        CategoryRepository $categoryRepository,
        int $id
    ): JsonResponse
    {
        try {
            return $this->json($categoryRepository->findById($id));
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new category.
     */
    #[Route('/api/category', methods: ['POST'], format: 'json')]
    #[OA\Tag(name: 'Category')]
    #[Security(name: 'Bearer')]
    public function create(
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] CategoryDto $categoryDto
    ): JsonResponse
    {
        try {
            $category = new Category();
            $category->setName($categoryDto->name)
                ->setDescription($categoryDto->description);

            $entityManager->persist($category);
            $entityManager->flush();

            $categoryRepository->clearCachedData();

            return $this->json([
                'id' => $category->getId(),
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a category.
     */
    #[Route('/api/category/{id}', methods: ['PUT'], format: 'json')]
    #[OA\Tag(name: 'Category')]
    #[Security(name: 'Bearer')]
    public function update(
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] CategoryDto $categoryDto,
        int $id
    ): JsonResponse
    {
        try {
            $category = $categoryRepository->findById($id);

            $category->setName($categoryDto->name)
                ->setDescription($categoryDto->description);

            $entityManager->persist($category);
            $entityManager->flush();

            $categoryRepository->clearCachedData();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete a category.
     */
    #[Route('/api/category/{id}', methods: ['DELETE'])]
    #[OA\Tag(name: 'Category')]
    #[Security(name: 'Bearer')]
    public function delete(
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse
    {
        try {
            $category = $categoryRepository->findById($id);
            $entityManager->remove($category);
            $entityManager->flush();

            $categoryRepository->clearCachedData();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
