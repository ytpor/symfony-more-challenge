<?php

namespace App\Controller;

use App\Entity\ItemAttribute;
use App\Model\ItemAttributeDto;
use App\Repository\ItemAttributeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class ItemAttributeController extends BaseController
{
    /**
     * List all attributes.
     */
    #[Route('/api/attribute', methods: ['GET'])]
    #[OA\Tag(name: 'Attribute')]
    #[Security(name: 'Bearer')]
    public function index(
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $attribute = $entityManager
            ->getRepository(ItemAttribute::class)
            ->findAll();

        return $this->json($attribute);
    }

    /**
     * Show an order.
     */
    #[Route('/api/attribute/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'Attribute')]
    #[Security(name: 'Bearer')]
    public function show(
        ItemAttributeRepository $attributeRepository,
        int $id
    ): JsonResponse
    {
        try {
            return $this->json($attributeRepository->findById($id));
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create an attribute.
     */
    #[Route('/api/attribute', methods: ['POST'], format: 'json')]
    #[OA\Tag(name: 'Attribute')]
    #[Security(name: 'Bearer')]
    public function create(
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] ItemAttributeDto $attributeDto
    ): JsonResponse
    {
        try {
            $attribute = new ItemAttribute();
            $attribute->setName($attributeDto->name)
                ->setDescription($attributeDto->description);

            $entityManager->persist($attribute);
            $entityManager->flush();

            return $this->json([
                'id' => $attribute->getId(),
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update an attribute.
     */
    #[Route('/api/attribute/{id}', methods: ['PUT'], format: 'json')]
    #[OA\Tag(name: 'Attribute')]
    #[Security(name: 'Bearer')]
    public function update(
        ItemAttributeRepository $attributeRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] ItemAttributeDto $attributeDto,
        int $id
    ): JsonResponse
    {
        try {
            $attribute = $attributeRepository->findById($id);

            $attribute->setName($attributeDto->name)
                ->setDescription($attributeDto->description);

            $entityManager->persist($attribute);
            $entityManager->flush();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete an attribute.
     */
    #[Route('/api/attribute/{id}', methods: ['DELETE'])]
    #[OA\Tag(name: 'Attribute')]
    #[Security(name: 'Bearer')]
    public function delete(
        ItemAttributeRepository $attributeRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse
    {
        try {
            $attribute = $attributeRepository->findById($id);
            $entityManager->remove($attribute);
            $entityManager->flush();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
