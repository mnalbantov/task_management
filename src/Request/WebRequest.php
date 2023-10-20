<?php

namespace App\Request;

use App\Repository\PaginatorTrait;
use Symfony\Component\HttpFoundation\Request;

final class WebRequest
{
    use PaginatorTrait;

    private int $page;
    private int $limitPerPage;
    private string $sorOrderBy;

    public static array $orderBy =
        [
            'ASC' => 'ASC',
            'DESC' => 'DESC',
        ];

    public static function getRequestFilters(Request $request): WebRequest
    {
        $page = $request->query->get('page') ?? null;
        $resultsPerPage = $request->query->get('resultsPerPage') ?? null;
        $orderBy = $request->query->get('orderBy') ?? null;

        $orderBy = self::getSorting($orderBy);
        $requestFilter = new self();

        if (null === $page) {
            $page = self::$defaultPage;
        }
        if (null === $resultsPerPage) {
            $resultsPerPage = self::$defaultLimit;
        }
        $requestFilter->setPage($page);
        $requestFilter->setLimitPerPage($resultsPerPage);
        $requestFilter->setSorOrderBy($orderBy);

        return $requestFilter;
    }

    private static function getSorting($orderBy)
    {
        $orderBy = strtoupper($orderBy);
        if (in_array($orderBy, self::$orderBy)) {
            return self::$orderBy[$orderBy];
        }

        // by default DESC
        return self::$orderBy['DESC'];
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getLimitPerPage(): int
    {
        return $this->limitPerPage;
    }

    public function setLimitPerPage(int $limitPerPage): void
    {
        $this->limitPerPage = $limitPerPage;
    }

    public function getSorOrderBy(): string
    {
        return $this->sorOrderBy;
    }

    public function setSorOrderBy(string $sorOrderBy): void
    {
        $this->sorOrderBy = $sorOrderBy;
    }

}
