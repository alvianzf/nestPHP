<?php

namespace App\routes\products;

class ProductsRepository
{
    private array $data = [
        ['id' => 1, 'name' => 'Apple'],
        ['id' => 2, 'name' => 'Banana']
    ];

    public function all(): array {
        return $this->data;
    }

    public function find($id): ?array {
        foreach ($this->data as $item) {
            if ($item['id'] == $id) return $item;
        }
        return null;
    }
}
