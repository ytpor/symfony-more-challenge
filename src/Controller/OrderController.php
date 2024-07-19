<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Model\ProductRatingDto;
use App\Model\OrderDto;
use App\Model\OrderUpdateDto;
use App\Repository\OrderRepository;
use App\Repository\OrderProductRepository;
use App\Repository\ProductRepository;
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

class OrderController extends BaseController
{
    /**
     * List all orders.
     */
    #[Route('/api/order', methods: ['GET'])]
    #[OA\Tag(name: 'Order')]
    #[Security(name: 'Bearer')]
    public function index(
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $order = $entityManager
            ->getRepository(Order::class)
            ->findAll();

        return $this->json($order);
    }

    /**
     * Show an order.
     */
    #[Route('/api/order/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'Order')]
    #[Security(name: 'Bearer')]
    public function show(
        OrderRepository $orderRepository,
        OrderProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse
    {
        try {
            $products = $productRepository->findByOrderId($id);
            $order = $orderRepository->findById($id);
            $order->setProducts($products);

            return $this->json($order);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create an order.
     */
    #[Route('/api/order', methods: ['POST'], format: 'json')]
    #[OA\Tag(name: 'Order')]
    #[Security(name: 'Bearer')]
    public function create(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        UserProfileRepository $profileRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] OrderDto $orderDto
    ): JsonResponse
    {
        try {
            // turn input into key value array for easy reference
            $reference = [];
            foreach ($orderDto->products as $product) {
                // Use this to make sure that the ids exists
                $productExists = $productRepository->findById($product['product_id']);
                $product_id = $product['product_id'];
                $quantity = $product['quantity'];
                if (isset($reference[$product_id])) {
                    $reference[$product_id] += $quantity;
                } else {
                    $reference[$product_id] = $quantity;
                }
            }

            // Use this to make sure that the ids exists
            $user = $userRepository->findById($orderDto->user_id);

            $profile = $profileRepository->findByUserId($orderDto->user_id);
            $products = $productRepository->findByIdJson($orderDto->products);

            $total = 0;
            foreach ($products as $product) {
                $product_id = $product->getId();
                $quantity = $reference[$product_id];

                $total += $product->getUnitPrice() * $quantity;
            }

            $order = new Order();
            $order->setUserId($orderDto->user_id)
                ->setName($profile->getName())
                ->setEmail($user->getEmail())
                ->setPhone($profile->getPhone())
                ->setAddress($profile->getAddress())
                ->setTotal($total)
                ->setStatus(Order::STATUS_NEW);

            $entityManager->persist($order);
            $entityManager->flush();

            $total = 0;
            foreach ($products as $product) {
                $product_id = $product->getId();
                $quantity = $reference[$product_id];

                $total = $product->getUnitPrice() * $quantity;

                $orderProduct = new OrderProduct();
                $orderProduct->setOrderId($order->getId())
                    ->setProductId($product->getId())
                    ->setAttributeId($product->getId()) // FIXME
                    ->setName($product->getName())
                    ->setDescription($product->getDescription())
                    ->setBrand($product->getBrand())
                    ->setModel($product->getModel())
                    ->setQuantity($quantity)
                    ->setUnitPrice($product->getUnitPrice())
                    ->setTotal($total);

                $entityManager->persist($orderProduct);
                $entityManager->flush();
            }

            return $this->json([
                'id' => $order->getId(),
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update order status.
     */
    #[Route('/api/order/status/{id}', methods: ['PUT'], format: 'json')]
    #[OA\Tag(name: 'Order')]
    #[Security(name: 'Bearer')]
    public function updateStatus(
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] OrderUpdateDto $orderDto,
        int $id
    ): JsonResponse
    {
        try {
            $order = $orderRepository->findById($id);

            $order->setStatus($orderDto->status);

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update order product rating.
     */
    #[Route('/api/order/product-rating/{id}', methods: ['PUT'], format: 'json')]
    #[OA\Tag(name: 'Order')]
    #[Security(name: 'Bearer')]
    public function updateRating(
        OrderRepository $orderRepository,
        OrderProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] ProductRatingDto $orderDto,
        int $id
    ): JsonResponse
    {
        try {
            // Use this to make sure that the ids exists
            $order = $orderRepository->findById($id);
            $product = $productRepository->findByOrderProductId($id, $orderDto->product_id);

            $product->setRating($orderDto->rating);

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete an order.
     */
    #[Route('/api/order/{id}', methods: ['DELETE'])]
    #[OA\Tag(name: 'Order')]
    #[Security(name: 'Bearer')]
    public function delete(
        EntityManagerInterface $entityManager,
        OrderRepository $orderRepository,
        int $id
    ): JsonResponse
    {
        try {
            $order = $orderRepository->findById($id);
            $entityManager->remove($order);
            $entityManager->flush();

            return $this->json('', JsonResponse::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
