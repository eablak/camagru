<?php

declare(strict_types=1);

// echo $_SERVER["REQUEST_URI"]; -> catch the url and send to routes
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

require "router.php";

$router = new Router();

$router->add("/", function(){
    echo "This is homepage";
});
$router->add("/about", function(){
    echo "This is about page";
});
$router->add("/products/{id}", function($id){
    echo "This is page for product $id";
});
$router->add("/products/{id}/orders/{oreder_id}", function($id, $order_id){
    echo "This is page for product $id and order $order_id";
});

$router->dispatch($path);


/*
switch($path){
    case "/":
        echo "This is homepage";
        break;
    case "/about":
        echo "This is about page";
        break;
    default:
        echo "Page not found";
} */

?>