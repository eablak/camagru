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
$router->get('/login', [UserController::class, 'login']);
$router->post('/login', [UserController::class, 'login']);
$router->get('/reset_password', [UserController::class, 'reset_password']);
$router->post('/reset_password', [UserController::class, 'reset_password']);
$router->get('/reset_password_mail', [UserController::class, 'reset_password_mail']);
$router->post('/reset_password_mail', [UserController::class, 'reset_password_mail']);
$router->get('/process_reset_password', [UserController::class, 'process_reset_password']);
$router->post('/process_reset_password', [UserController::class, 'process_reset_password']);

echo $router->resolve();

?>