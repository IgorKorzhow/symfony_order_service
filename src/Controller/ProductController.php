<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $repository,
        private readonly SerializerInterface $serializer,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/product', name: 'products')]
    public function index(): JsonResponse
    {
        return new JsonResponse(
            data: $this->serializer->serialize($this->repository->getPaginated([]), 'json'),
            status: Response::HTTP_OK
        );
    }
}
