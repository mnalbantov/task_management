<?php

namespace App\Response\Formatter;

use Knp\Component\Pager\Pagination\PaginationInterface;

interface ResponseFormatterInterface
{
    public function formatListItems(PaginationInterface $pagination);
}
