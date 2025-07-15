<?php

namespace App\Controller;

use App\Dto\Product\ProductQueryDto;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly ProductRepository $repository,
        private readonly SerializerInterface $serializer,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    #[Route('/api/products', name: 'products')]
    public function index(ProductQueryDto $dto): JsonResponse
    {
        $dto->validate($this->validator);

        return new JsonResponse(
            data: $this->serializer->normalize($this->repository->getPaginated($dto->getPage(), $dto->getPerPage()), 'json'),
            status: Response::HTTP_OK
        );
    }
}
