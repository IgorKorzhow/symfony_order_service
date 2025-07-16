<?php

namespace App\Controller;

use App\Dto\Basket\BasketProductDto;
use App\Exception\DtoValidationException;
use App\Repository\ProductRepository;
use App\Service\Basket\BasketServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BasketController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly BasketServiceInterface $basketService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/basket', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $basket = $this->basketService->getBasket($this->getUser()->getId());

        return new JsonResponse($this->serializer->normalize($basket, 'json'), Response::HTTP_OK);
    }

    /**
     * @throws ExceptionInterface
     * @throws DtoValidationException
     */
    #[Route('/api/basket/products', name: 'basket_products_change', methods: ['PATCH'])]
    public function changeProduct(BasketProductDto $basketProduct): JsonResponse
    {
        $basketProduct->validate($this->validator);

        $product = $this->productRepository->findOneBy(['id' => $basketProduct->getProductId()]);

        $basketProduct->setPrice($product->getCost());

        $basket = $this->basketService->getBasket($this->getUser()->getId());
        $basket = $this->basketService->changeProduct($basket, $basketProduct);

        return new JsonResponse($this->serializer->normalize($basket, 'json'), Response::HTTP_OK);
    }
}
