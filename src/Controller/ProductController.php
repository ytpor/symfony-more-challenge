<?php

namespace App\Controller;

use App\Entity\Product;
use App\Model\ProductDto;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends BaseController
{
    /**
     * List all products.
     */
    #[Route('/api/product', methods: ['GET'])]
    #[OA\Tag(name: 'Product')]
    #[Security(name: 'Bearer')]
    public function index(
        ProductRepository $productRepository,
    ): JsonResponse
    {
        $product = $productRepository->findAllBy();

        return $this->json($product);
    }

    /**
     * Show a product.
     */
    #[Route('/api/product/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'Product')]
    #[Security(name: 'Bearer')]
    public function show(
        ProductRepository $productRepository,
        int $id
    ): JsonResponse
    {
        try {
            return $this->json($productRepository->findById($id));
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show products by category.
     */
    #[Route('/api/product/category/{category_id}', methods: ['GET'])]
    #[OA\Tag(name: 'Product')]
    #[Security(name: 'Bearer')]
    public function showByCategory(
        ProductRepository $productRepository,
        int $category_id
    ): JsonResponse
    {
        try {
            return $this->json($productRepository->findByCategoryId($category_id));
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new product.
     */
    #[Route('/api/product', methods: ['POST'], format: 'json')]
    #[OA\Tag(name: 'Product')]
    #[Security(name: 'Bearer')]
    public function create(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] ProductDto $productDto
    ): JsonResponse
    {
        try {
            // Use this to make sure that the ids exists
            $category = $categoryRepository->findById($productDto->category_id);

            $product = new Product();
            $product->setCategoryId($productDto->category_id)
                ->setName($productDto->name)
                ->setDescription($productDto->description)
                ->setBrand($productDto->brand)
                ->setModel($productDto->model)
                ->setUnitPrice($productDto->unit_price);

            $entityManager->persist($product);
            $entityManager->flush();

            $productRepository->clearCachedData();

            return $this->json([
                'id' => $product->getId(),
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a product.
     */
    #[Route('/api/product/{id}', methods: ['PUT'], format: 'json')]
    #[OA\Tag(name: 'Product')]
    #[Security(name: 'Bearer')]
    public function update(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] ProductDto $productDto,
        int $id
    ): JsonResponse
    {
        try {
            // Use this to make sure that the ids exists
            $category = $categoryRepository->findById($productDto->category_id);

            $product = $productRepository->findById($id);

            $product->setCategoryId($productDto->category_id)
                ->setName($productDto->name)
                ->setDescription($productDto->description)
                ->setBrand($productDto->brand)
                ->setModel($productDto->model)
                ->setUnitPrice($productDto->unit_price);

            $entityManager->persist($product);
            $entityManager->flush();

            $productRepository->clearCachedData();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete a producct.
     */
    #[Route('/api/product/{id}', methods: ['DELETE'])]
    #[OA\Tag(name: 'Product')]
    #[Security(name: 'Bearer')]
    public function delete(
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        int $id
    ): JsonResponse
    {
        try {
            $product = $productRepository->findById($id);
            $entityManager->remove($product);
            $entityManager->flush();

            $productRepository->clearCachedData();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
