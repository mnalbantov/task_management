<?php

namespace App\Response;

use Knp\Component\Pager\Pagination\PaginationInterface;

class PaginatedResponse implements \JsonSerializable
{
    private int $page;
    private int $limitPerPage;
    private int $pages;
    private int $totalItems;
    private ?array $results;

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

    public function getPages(): int
    {
        return $this->pages;
    }

    public function setPages(int $pages): void
    {
        $this->pages = $pages;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function setTotalItems(int $totalItems): void
    {
        $this->totalItems = $totalItems;
    }

    public function getData(): ?array
    {
        return $this->results;
    }

    public function setData(?array $data): void
    {
        $this->results = $data;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}