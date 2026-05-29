<?php

namespace App\Service;

trait PaginationTrait
{
    private const PER_PAGE = 20;

    /** @return array<int|null> null means ellipsis */
    private function buildPageLinks(int $page, int $lastPage): array
    {
        $show = array_fill_keys([1, $lastPage], true);
        for ($i = max(1, $page - 2); $i <= min($lastPage, $page + 2); $i++) {
            $show[$i] = true;
        }
        ksort($show);

        $links = [];
        $prev = 0;
        foreach (array_keys($show) as $p) {
            if ($p - $prev > 1) {
                $links[] = null;
            }
            $links[] = $p;
            $prev = $p;
        }

        return $links;
    }

    private function paginationData(int $total, int $page): array
    {
        $lastPage = max(1, (int) ceil($total / self::PER_PAGE));

        return [
            'page'      => $page,
            'lastPage'  => $lastPage,
            'total'     => $total,
            'pageLinks' => $this->buildPageLinks($page, $lastPage),
        ];
    }
}
