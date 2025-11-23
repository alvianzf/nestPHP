# routes/ README

This folder contains all feature modules for this stupid framework.  
Each module is a directory containing a controller, and optionally a service and repository. Your framework will autoload every module and scan its controller for **PHP Attributes** that define routes.

## Purpose

`routes/` defines HTTP entry points using modern PHP attributes, similar to NestJS decorators.  
Controllers declare endpoints with attributes like `#[Get('/path')]` and `#[Post('/path')]`, while services and repositories keep logic clean and layered.

## Folder Structure

```
App/
  routes/
    products/
      productsController.php
      productsService.php
      productsRepository.php
    users/
      usersController.php
      usersService.php
      usersRepository.php
    README.md
```

Each folder is a module.

## Controller Attributes

Controllers define routes using the framework’s HTTP attributes:

- `#[Get('/path')]`
- `#[Post('/path')]`
- `#[Put('/path')]`
- `#[Delete('/path')]`

### Example

```php
<?php

namespace App\routes\products;

use Framework\Core\Attributes\Get;
use Framework\Core\Attributes\Post;

class ProductsController {

    private ProductsService $service;

    public function __construct(ProductsService $service) {
        $this->service = $service;
    }

    #[Get('/')]
    public function getAll() {
        return $this->service->findAll();
    }

    #[Post('/')]
    public function create() {
        return $this->service->create(request()->body());
    }
}
```

Your framework will automatically:

1. Instantiate the controller  
2. Read all attributes via Reflection  
3. Map `#[Get('/')]` to the method  
4. Register the route with the router  

Exactly like NestJS but lighter, faster, and with fewer meetings.

## Services

Business logic lives here.

Example:

```php
<?php

namespace App\routes\products;

class ProductsService {
    public static function findAll() {
        return ProductsRepository::getAll();
    }

    public static function create($payload) {
        return ProductsRepository::insert($payload);
    }
}
```

## Repositories

Handles database or storage access only.

Example:

```php
<?php

namespace App\routes\products;

class ProductsRepository {
    public static function getAll() {
        // return DB::query(...)
    }
}
```

## Autoloading Behaviour

During bootstrap, your framework does this:

1. Recursively scan `App/routes/`  
2. For each folder, find the `*.controller.php`  
3. Use PHP Reflection to read attributes  
4. Register each method + HTTP verb + URI  
5. Bind controller instance  
6. Move on with life  

No manual imports. No "add this to a config file".  
Just drop a module in and it works.

## Adding a New Module

1. Create a folder under `App/routes/yourFeature/`  
2. Add `yourFeature.controller.php` with attributes  
3. Add service/repository if needed  
4. You're done  

The bootstrapper handles the rest.

## Rules

- Controllers handle request input and return response arrays or DTOs  
- Services contain business logic  
- Repositories handle DB/storage  
- No circular imports  
- No controller fatness unless you want to punish future interns  

## TLDR

Controllers: `#[Get('/')]` and friends  
Services: logic  
Repositories: data  
Framework: autoload and handle everything  
You: go drink kopi and pretend this was easy
