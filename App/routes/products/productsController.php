<?php

namespace App\routes\products;

use Framework\Core\BaseController;
use Framework\Core\Attributes\Get;

class ProductsController extends BaseController
{
    public function __construct(private ProductsService $service) {}

    #[Get('/')]
    public function getAll() {
        return $this->response($this->service->findAll());
    }

    #[Get('/{id}')]
    public function getOne($id) {
        $product = $this->service->findOne($id);
        if (!$product) return $this->response(['error' => 'Not found'], 404);
        return $this->response($product);
    }
}
