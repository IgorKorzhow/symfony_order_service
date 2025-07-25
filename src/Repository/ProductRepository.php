<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use App\Enum\OrderStatusEnum;
use App\Service\Paginator\PaginatedListEntity;
use App\Service\Paginator\PaginateQueryService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    private readonly PaginateQueryService $paginateQueryService;

    public function __construct(ManagerRegistry $registry, PaginateQueryService $paginateQueryService)
    {
        parent::__construct($registry, Product::class);

        $this->paginateQueryService = $paginateQueryService;
    }

    public function store(Product $product): Product
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();

        return $product;
    }

    /**
     * @throws \Exception
     */
    public function getPaginated(int $page, int $perPage): PaginatedListEntity
    {
        $baseQuery = $this->createQueryBuilder('product')
            ->orderBy('product.id', 'DESC');

        return $this->paginateQueryService->paginate($baseQuery, $page, $perPage);
    }

    public function getOrderedProductsInDatePeriodIterator(\DateTimeImmutable $dateFrom, \DateTimeImmutable $dateTo, int $batchSize = 50): iterable
    {
        $lastId = 0;

        do {
            $qb = $this->createQueryBuilder('product')
                ->innerJoin('product.orderItems', 'order_items')
                ->addSelect('order_items')
                ->innerJoin('order_items.order', 'ord')
                ->addSelect('ord')
                ->where('ord.payedAt BETWEEN :dateFrom AND :dateTo')
                ->andWhere('ord.orderStatus != :status')
                ->andWhere('product.id > :lastId')
                ->setParameter('dateFrom', $dateFrom->format('Y-m-d'))
                ->setParameter('dateTo', $dateTo->format('Y-m-d'))
                ->setParameter('status', OrderStatusEnum::CREATED->value)
                ->setParameter('lastId', $lastId)
                ->orderBy('product.id', 'ASC')
                ->setMaxResults($batchSize);

            $results = $qb->getQuery()->getResult();

            foreach ($results as $product) {
                yield $product;
                $lastId = $product->getId();
            }
        } while (count($results) === $batchSize);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
