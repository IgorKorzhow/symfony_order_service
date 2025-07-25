<?php

namespace App\Controller;

use App\Dto\Mappers\Basket\BasketDtoMapper;
use App\Dto\RequestDto\Basket\BasketChangeProductRequestDto;
use App\Entity\BasketProduct;
use App\Repository\ProductRepository;
use App\Service\Basket\BasketServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class BasketController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly BasketServiceInterface $basketService,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/basket', name: 'index', methods: ['GET'])]
    public function index(BasketDtoMapper $basketDtoMapper): JsonResponse
    {
        $basket = $this->basketService->getBasket($this->getUser()->getId());

        return new JsonResponse(
            data: $basketDtoMapper->entityToDto($basket),
            status: Response::HTTP_OK,
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/basket/products', name: 'basket_products_change', methods: ['PATCH'])]
    public function changeProduct(
        #[MapRequestPayload]
        BasketChangeProductRequestDto $requestDto,
        BasketDtoMapper $basketDtoMapper,
    ): JsonResponse
    {
        $basket = $this->basketService->getBasket($this->getUser()->getId());

        $product = $this->productRepository->findOneBy(['id' => $requestDto->productId]);

        $basket = $this->basketService->changeProduct(
            basket: $basket,
            basketProduct: new BasketProduct(
                productId: $requestDto->productId,
                count: $requestDto->count,
                price: $product->getCost(),
            ),
        );

        return new JsonResponse(
            data: $basketDtoMapper->entityToDto($basket),
            status: Response::HTTP_OK,
        );
    }
}
