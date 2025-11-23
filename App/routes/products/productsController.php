<?php

namespace App\routes\products;

use Framework\Core\Attributes\Get;

class ProductsController {

    #[Get('{id}')]
    public function getOne($id) {
        return [
            'id' => $id,
            'status' => 'ok'
        ];
    }
}
