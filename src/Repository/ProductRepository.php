<?php

namespace App\Repository;

use App\Entity\Product;
use App\Enum\OrderStatusEnum;
use App\Service\Paginator\PaginatedListEntity;
use App\Service\Paginator\PaginateQueryService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    private readonly PaginateQueryService $paginateQueryService;

    public function __construct(ManagerRegistry $registry,  PaginateQueryService $paginateQueryService)
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
     * @throws Exception
     */
    public function getPaginated(array $query): PaginatedListEntity
    {
        $baseQuery = $this->createQueryBuilder('product')
            ->orderBy('product.id', 'DESC');

        return $this->paginateQueryService->paginate($baseQuery);
    }

    public function getOrderedProductsInDatePeriodIterator(\DateTimeImmutable $dateFrom, \DateTimeImmutable $dateTo): iterable
    {
        $baseQuery = $this->createQueryBuilder('product')
            ->innerJoin('product.orderItems', 'order_items')
            ->addSelect('order_items')
            ->innerJoin('order_items.order', 'order')
            ->addSelect('order')
            ->where('order.payedAt BETWEEN :dateFrom AND :dateTo')
            ->andWhere('order.orderStatus != :status')
            ->setParameter('dateFrom', $dateFrom->format('Y-m-d'))
            ->setParameter('dateTo', $dateTo->format('Y-m-d'))
            ->setParameter('status', OrderStatusEnum::CREATED->value)
            ->getQuery();

        return $baseQuery->toIterable();

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
