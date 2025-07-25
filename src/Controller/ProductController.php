<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Mappers\Product\ProductDtoMapper;
use App\Dto\RequestDto\Product\ProductIndexRequestDto;
use App\Dto\ResponseDto\PaginatedListEntityResponseDto;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $repository,
    ) {
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/products', name: 'products')]
    public function index(
        #[MapQueryString]
        ProductIndexRequestDto $requestDto,
        ProductDtoMapper $productDtoMapper,
    ): JsonResponse {
        $paginatedEntityData = $this->repository->getPaginated($requestDto->page, $requestDto->perPage);

        return new JsonResponse(
            data: new PaginatedListEntityResponseDto(
                page: $paginatedEntityData->getPage(),
                perPage: $paginatedEntityData->getPerPage(),
                total: $paginatedEntityData->getTotal(),
                data: $productDtoMapper->arrayEntityToDto($paginatedEntityData->getData()),
                totalPages: $paginatedEntityData->getTotalPages(),
            ),
            status: Response::HTTP_OK
        );
    }
}
