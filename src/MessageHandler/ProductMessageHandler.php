<?php

namespace App\MessageHandler;

use App\Entity\Product;
use App\Message\Product\ProductMessage;
use App\Repository\ProductRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ProductMessageHandler
{
    public function __construct(private ProductRepository $repository)
    {
    }

    public function __invoke(ProductMessage $message): void
    {
        $product = $this->repository->findOneBy(['external_id' => $message->getId()]);

        if ($product === null) {
            $product = new Product();
        }

        $product->setName($message->getName());
        $product->setDescription($message->getDescription());
        $product->setMeasurements($message->getMeasurements());
        $product->setTax($message->getTax());
        $product->setExternalId($message->getId());
        $product->setVersion($message->getVersion());
        $product->setCost($message->getCost());

        $this->repository->store($product);
    }
}

