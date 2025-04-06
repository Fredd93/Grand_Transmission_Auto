<?php
// app/public/routes/api.php

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
?>