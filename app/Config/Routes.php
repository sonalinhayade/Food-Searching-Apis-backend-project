<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('/auth/register', 'AuthController::registerAuth');
$routes->post('/auth/login', "AuthController::loginAuth");
$routes->get('/auth/logout', 'AuthController::logoutAuth', ['filter' => 'jwt_auth']);

$routes->group('food', ["filter" => "jwt_auth"], function ($routes) {
    $routes->get('/', "FoodSearchController::getAllProducts");
    $routes->get('price', 'FoodSearchController::filterPrice');
    $routes->get('rating', 'FoodSearchController::filterRatings');
    $routes->get('category', 'FoodSearchController::filterCategory');
    $routes->get('topping', 'FoodSearchController::filterTopping');
    $routes->get('type', 'FoodSearchController::filterType');
});