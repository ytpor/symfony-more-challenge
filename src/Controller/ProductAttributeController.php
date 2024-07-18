<?php

namespace App\Controller;

use App\Entity\ProductAttribute;
use App\Model\ProductAttributeDto;
use App\Repository\ItemAttributeRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductAttributeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class ProductAttributeController  extends BaseController
{
    /**
     * List all product attributes.
     */
    #[Route('/api/product-attribute', methods: ['GET'])]
    #[OA\Tag(name: 'Product Attribute')]
    #[Security(name: 'Bearer')]
    public function index(
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $productAttribute = $entityManager
            ->getRepository(ProductAttribute::class)
            ->findAll();

        return $this->json($productAttribute);
    }

    /**
     * Show a product attribute.
     */
    #[Route('/api/product-attribute/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'Product Attribute')]
    #[Security(name: 'Bearer')]
    public function show(
        ProductAttributeRepository $productAttributeRepository,
        int $id
    ): JsonResponse
    {
        try {
            return $this->json($productAttributeRepository->findById($id));
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new product attribute.
     */
    #[Route('/api/product-attribute', methods: ['POST'], format: 'json')]
    #[OA\Tag(name: 'Product Attribute')]
    #[Security(name: 'Bearer')]
    public function create(
        ItemAttributeRepository $attributeRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] ProductAttributeDto $productAttributeDto
    ): JsonResponse
    {
        try {
            // Use this to make sure that the ids exists
            $product = $productRepository->findById($productAttributeDto->product_id);
            $attribute = $attributeRepository->findById($productAttributeDto->attribute_id);

            $productAttribute = new ProductAttribute();
            $productAttribute->setProductId($productAttributeDto->product_id)
                ->setAttributeId($productAttributeDto->attribute_id);

            $entityManager->persist($productAttribute);
            $entityManager->flush();

            return $this->json([
                'id' => $productAttribute->getId(),
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a product attribute.
     */
    #[Route('/api/product-attribute/{id}', methods: ['PUT'], format: 'json')]
    #[OA\Tag(name: 'Product Attribute')]
    #[Security(name: 'Bearer')]
    public function update(
        ItemAttributeRepository $attributeRepository,
        ProductRepository $productRepository,
        ProductAttributeRepository $productAttributeRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] ProductAttributeDto $productAttributeDto,
        int $id
    ): JsonResponse
    {
        try {
            // Use this to make sure that the ids exists
            $product = $productRepository->findById($productAttributeDto->product_id);
            $attribute = $attributeRepository->findById($productAttributeDto->attribute_id);

            $productAttribute = $productAttributeRepository->findById($id);

            $productAttribute->setProductId($productAttributeDto->product_id)
                ->setAttributeId($productAttributeDto->attribute_id);

            $entityManager->persist($productAttribute);
            $entityManager->flush();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete a product attribute.
     */
    #[Route('/api/product-attribute/{id}', methods: ['DELETE'])]
    #[OA\Tag(name: 'Product Attribute')]
    #[Security(name: 'Bearer')]
    public function delete(
        ProductAttributeRepository $productAttributeRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse
    {
        try {
            $productAttribute = $productAttributeRepository->findById($id);
            $entityManager->remove($productAttribute);
            $entityManager->flush();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
