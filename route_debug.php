<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
foreach($app['router']->getRoutes() as $route) {
    if ($route->uri() === 'admin/exams/{exam}/subjects/{subject}/marks' && in_array('POST', $route->methods())) {
        echo 'URI: ' . $route->uri() . PHP_EOL;
        echo 'Name: ' . $route->getName() . PHP_EOL;
        echo 'Action: ' . $route->getActionName() . PHP_EOL;
        echo 'Methods: ' . implode(',', $route->methods()) . PHP_EOL;
    }
}
