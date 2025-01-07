<?php

namespace App\Controller\Api;

use App\Service\WishlistService;
use App\Dto\WishlistDto;
use App\Dto\ProductDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;


#[Route('/api/wishlist', name: 'wishlist_')]
class WishlistController extends AbstractController
{
    private WishlistService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    #[Route('/all', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $items = $this->wishlistService->getAllWishlistItems();
        $wishlistDtos = array_map(
            fn($item) => new WishlistDto($item),
            $items
        );

        return $this->json($wishlistDtos);
    }

    #[Route('', name: 'list_product_by_user', methods: ['GET'])]
    public function listProductByUser(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');
        if (!$user){
            throw new AppException('E2025');
        }

        $products = $this->wishlistService->getProductsByUser($user);
        $productDtos = array_map(fn($product) => new ProductDto($product), $products);
        return $this->json($productDtos);
    }

    #[Route('/{id}', name: 'byId', methods: ['GET'])]
    public function byId(int $id): JsonResponse
    {
        $item = $this->wishlistService->getWishlistItemById($id);

        if (!$item) {
            return $this->json(['message' => 'Wishlist item not found'], 404);
        }

        return $this->json(new WishlistDto($item));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $item = $this->wishlistService->createWishlistItem($data);
        return $this->json(new WishlistDto($item), 201);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->wishlistService->deleteWishlistItem($id);

        return $this->json(['message' => 'Wishlist item deleted']);
    }
}
