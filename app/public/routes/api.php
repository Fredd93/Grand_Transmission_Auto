<?php
require_once __DIR__ . '/../api/OrderApiController.php';

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
?>