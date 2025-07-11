<?php

namespace App\Service\Paginator;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class PaginateQueryService
{
    /**
     * @throws \Exception
     */
    public function paginate(QueryBuilder $queryBuilder, int $page = 1, ?int $limit = null): PaginatedListEntity
    {
        $queryBuilder = $queryBuilder
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);


        $paginator  = new Paginator($queryBuilder);

        $totalItems = count($paginator);

        return new PaginatedListEntity(
            page: $page,
            perPage: $limit,
            total: $totalItems,
            data: iterator_to_array($paginator->getIterator()),
        );
    }
}
