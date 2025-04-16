<?php
// app/public/api/get_cars.php

header('Content-Type: application/json');

// Include the CarModel
require_once __DIR__ . '/../models/CarModel.php';

$carModel = new CarModel();

// Read filter parameters from GET (or POST if you prefer)
$filters = [
    'brand' => $_GET['brand'] ?? null,
    'year' => $_GET['year'] ?? null,
    'transmission' => $_GET['transmission'] ?? null,
    'on_sale' => $_GET['on_sale'] ?? null,
    'price_min' => $_GET['price_min'] ?? null,
    'price_max' => $_GET['price_max'] ?? null
];

// Get filtered cars using your model method
$cars = $carModel->getFilteredCars($filters);

// Convert CarDTO objects into an array for JSON output
$output = [];
foreach ($cars as $car) {
    $output[] = [
        'car_id'      => $car->getCarId(),
        'brand'       => $car->getBrand(),
        'model'       => $car->getModel(),
        'price'       => $car->getPrice(),
        'on_sale'     => $car->getOnSale(),
        'discount'    => $car->getDiscount(),
        'image_path'  => $car->getImage()
        // Add additional fields if needed (e.g., year, transmission)
    ];
}

// Output the JSON
echo json_encode($output);
exit;
?>
