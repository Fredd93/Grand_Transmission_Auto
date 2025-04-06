<?php
require_once __DIR__ . '/../api/OrderApiController.php';
require_once __DIR__ . '/../api/CarApiController.php';


// Route for API login endpoint
Route::add('/api/login', function () {
    require __DIR__ . '/../../api/login.php';
});
Route::add('/api/car_filter', function () {
    require __DIR__ . '/../api/car_filter.php';
});
Route::add('/api/get_cars', function () {
    require __DIR__ . '/../api/get_cars.php';
});
Route::add('/api/add_car', function () {
    require __DIR__ . '/../../api/add_car.php';
});

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
}, 'post');

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


?>