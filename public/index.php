<?php

spl_autoload_register(function ($className){

    $base_dir = dirname(__DIR__) . '/';
    $className = str_replace('\\', '/', $className);
    $file = $base_dir . $className . '.php';

    if (file_exists($file)){
        include $file;
    }

});

include ("database.php");


use App\Router;
use App\Controllers\HomeController;
use App\Controllers\UserController;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/register', [UserController::class, 'register']);
$router->post('/register', [UserController::class, 'register']);
$router->get('/activate_account', [UserController::class, 'activate_account']);

echo $router->resolve();

?>