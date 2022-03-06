<?php

declare(strict_types=1);

require __DIR__ . '\..\vendor\autoload.php';
require_once __DIR__ . '\..\src\Utils\debug.php';

use App\src\Controller;

$app = new Controller();
$app->run();
