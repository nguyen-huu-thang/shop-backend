<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $data): Product
    {
        $product = new Product();
        $product->setName($data['name'] ?? throw new \Exception('Name is required'))
                ->setDescription($data['description'] ?? null)
                ->setPrice($data['price'] ?? throw new \Exception('Price is required'))
                ->setStock($data['stock'] ?? throw new \Exception('Stock is required'))
                ->setUniqueFeatures($data['uniqueFeatures'] ?? null)
                ->setIsFeatured($data['isFeatured'] ?? false)
                ->setCity($data['city'] ?? null)
                ->setDistrict($data['district'] ?? null);

        // Set category if provided
        if (!empty($data['categoryId'])) {
            $category = $this->categoryRepository->find($data['categoryId']);
            if (!$category) {
                throw new \Exception('Invalid category ID');
            }
            $product->setCategory($category);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->getProductById($id);

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $product->setName($data['name'] ?? $product->getName())
                ->setDescription($data['description'] ?? $product->getDescription())
                ->setPrice($data['price'] ?? $product->getPrice())
                ->setStock($data['stock'] ?? $product->getStock())
                ->setUniqueFeatures($data['uniqueFeatures'] ?? $product->getUniqueFeatures())
                ->setIsFeatured($data['isFeatured'] ?? $product->getIsFeatured())
                ->setCity($data['city'] ?? $product->getCity())
                ->setDistrict($data['district'] ?? $product->getDistrict());

        // Update category if provided
        if (array_key_exists('categoryId', $data)) {
            $category = $this->categoryRepository->find($data['categoryId']);
            if (!$category && $data['categoryId'] !== null) {
                throw new \Exception('Invalid category ID');
            }
            $product->setCategory($category);
        }

        $this->entityManager->flush();

        return $product;
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->getProductById($id);

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    public function getProductsByCategoryId(int $categoryId): array
    {
        return $this->productRepository->findByCategoryId($categoryId);
    }
}
