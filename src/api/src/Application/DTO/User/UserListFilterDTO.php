<?php

namespace DLDelivery\Application\DTO\User;

class UserListFilterDTO
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 10,
        public ?int $totalPages = null,
        public ?array $filters = []
    ) {
        $this->validateFilters();
    }

    private function validateFilters()
    {
        if (empty($this->filters)) { return; }

        $allowedFilters = ['access', 'username', 'name'];
        $validFilters = array_intersect_key($this->filters, array_flip($allowedFilters));

        if (empty($validFilters)) { throw new \InvalidArgumentException("No valid filters provided"); }

        $this->filters = $validFilters;
    }
}