<?php

require_once __DIR__ . '/../api/OrderApiController.php';
require_once __DIR__ . '/../api/CarApiController.php';


// AUTH ROUTES
Route::add('/api/login', function () {
    require_once __DIR__ . '/../api/utils/AuthApiController.php';
    (new AuthApiController())->login();
}, 'POST');

Route::add('/api/logout', function () {
    require_once __DIR__ . '/../api/utils/AuthApiController.php';
    (new AuthApiController())->logout();
}, 'POST');

// ORDER ROUTES
Route::add('/api/orders', function () {
    $controller = new OrderApiController();
    $controller->getAllOrders();
}, 'GET');

Route::add('/api/orders/status', function () {
    $controller = new OrderApiController();
    $controller->updateOrderStatus();
}, 'PUT');

Route::add('/api/orders/([0-9]+)', function ($orderId) {
    $controller = new OrderApiController();
    $controller->getOrderById($orderId);
}, 'GET');

// CAR ROUTES (ALL use /api/CarApiController.php)
require_once __DIR__ . '/../api/CarApiController.php';

Route::add('/api/cars', function () {
    (new CarApiController())->getAllCars();
});

Route::add('/api/cars/featured', function () {
    (new CarApiController())->getFeaturedCars();
});

Route::add('/api/cars/new', function () {
    (new CarApiController())->getNewArrivals();
});

Route::add('/api/cars/deals', function () {
    (new CarApiController())->getDealsOfDay();
});

Route::add('/api/cars/filters', function () {
    (new CarApiController())->getFilterOptions();
});

Route::add('/api/cars/filter', function () {
    (new CarApiController())->getFilteredCars();
});

Route::add('/api/cars', function () {
    (new CarApiController())->insertCar();
}, 'POST');

Route::add('/api/cars/([0-9]+)', function ($id) {
    (new CarApiController())->getCarById($id);
});
Route::add('/api/cars/edit', function () {
    $controller = new CarApiController();
    $controller->updateCarDetails();
});

Route::add('/api/orders/create', function () {
    $controller = new OrderApiController();
    $controller->createOrder();
}, 'post');


Route::add('/api/cars/featured', function () {
    (new CarApiController())->getFeaturedCars();
}, 'GET');

Route::add('/api/cars/new', function () {
    (new CarApiController())->getNewArrivals();
}, 'GET');

Route::add('/api/cars/deals', function () {
    (new CarApiController())->getDealsOfDay();
}, 'GET');

Route::add('/api/cars/filters', function () {
    (new CarApiController())->getFilterOptions();
}, 'GET');

Route::add('/api/cars/filter', function () {
    (new CarApiController())->getFilteredCars();
}, 'POST');
