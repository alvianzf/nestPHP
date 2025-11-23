<?php

require __DIR__ . '/../vendor/autoload.php';

use Framework\Core\Kernel;

$kernel = new Kernel();
$kernel->handle();
