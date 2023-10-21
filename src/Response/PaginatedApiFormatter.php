<?php

namespace App\Response;

use Knp\Component\Pager\Pagination\PaginationInterface;

trait PaginatedApiFormatter
{
    // used for API responses mainly
    public function format(PaginationInterface $pagination): array
    {
        $pagesCount = ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage());
        $paginatedResponse = new PaginatedResponse();
        $paginatedResponse->setPage($pagination->getCurrentPageNumber());
        $paginatedResponse->setPages($pagesCount);
        $paginatedResponse->setTotalItems($pagination->getTotalItemCount());
        $paginatedResponse->setData($pagination->getItems());

        return $paginatedResponse->jsonSerialize();
    }
}