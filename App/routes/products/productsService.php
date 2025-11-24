<?php

namespace App\routes\products;

class ProductsService
{
    public function __construct(private ProductsRepository $repo) {}

    public function findAll(): array {
        return $this->repo->all();
    }

    public function findOne($id): ?array {
        return $this->repo->find($id);
    }
}
