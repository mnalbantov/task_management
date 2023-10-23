<?php

namespace App\Response\Formatter;

use App\Response\PaginatedResponse;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ResponseApiFormatter implements ResponseFormatterInterface
{
    public function formatListItems(PaginationInterface $pagination): array
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
