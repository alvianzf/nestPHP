<?php

namespace App\routes\products;

use Framework\Core\Attributes\Get;
use Framework\Core\Attributes\Post;

class ProductsController {

    #[Get('/products')]
    public function getAll() {
        return ['status' => 'ok'];
    }

    #[Post('/products')]
    public function create() {
        return ['created' => true];
    }
}
