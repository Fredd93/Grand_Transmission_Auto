<?php
// app/public/api/car_filter.php

header('Content-Type: application/json');

require_once __DIR__ . '/../models/CarModel.php';

$carModel = new CarModel();

$data = [
    'brands' => $carModel->getDistinctBrands(),
    'years' => $carModel->getDistinctYears(),
    'transmissions' => $carModel->getDistinctTransmissions(),
    'on_sale_values' => $carModel->getDistinctOnSaleValues(),
    'price_bounds' => $carModel->getPriceBounds()
];

echo json_encode($data);
exit;
?>
