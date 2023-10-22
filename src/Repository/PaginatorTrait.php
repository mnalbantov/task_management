<?php

namespace App\Repository;

use App\Response\PaginatedResponse;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait PaginatorTrait
{
    public static int $defaultPage = 1;
    public static int $defaultLimit = 20;

    protected function usePaginatedResponse(QueryBuilder $queryBuilder, int $page, int $perPage): array
    {
        if ($page < 0) {
            $page = 1;
        }
        if ($perPage < 0) {
            $perPage = self::$defaultLimit;
        }
        if (!$page) {
            $page = self::$defaultPage;
        }
        if (!$perPage) {
            $perPage = self::$defaultLimit; // as default
        }
        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $perPage);
        $paginator
            ->getQuery()
            ->setFirstResult($perPage * ($page - 1)) // set the offset
            ->setMaxResults($perPage);

        $paginatedData = [];
        foreach ($paginator as $item) {
            $paginatedData[] = $item;
        }

        $paginatedResponse = new PaginatedResponse();
        $paginatedResponse->setPage($page);
        $paginatedResponse->setPages($pagesCount);
        $paginatedResponse->setTotalItems($totalItems);
        $paginatedResponse->setData($paginatedData);

        return $paginatedResponse->jsonSerialize();
    }
}
