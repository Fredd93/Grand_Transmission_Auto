<?php
// app/public/api/add_car.php

header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/CarController.php';

$controller = new CarController();

// Validate required fields (excluding image)
$required = ['brand', 'model', 'year', 'transmission', 'price', 'on_sale', 'discount', 'status'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Field '$field' is required."]);
        exit;
    }
}

// Check image upload
if (!isset($_FILES['image_path']) || $_FILES['image_path']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => "An image file is required."]);
    exit;
}

// Handle image upload
$uploadDir = __DIR__ . '/../../assets/images/';
$uploadedFile = $_FILES['image_path'];
$filename = uniqid() . '_' . basename($uploadedFile['name']);
$targetFile = $uploadDir . $filename;

if (!move_uploaded_file($uploadedFile['tmp_name'], $targetFile)) {
    echo json_encode(['success' => false, 'message' => "Failed to move uploaded file."]);
    exit;
}

// Set image path to be stored in the database
$image_path = "../../assets/images/" . $filename;

// Gather and sanitize form data
$data = [
    'brand'            => $_POST['brand'],
    'model'            => $_POST['model'],
    'year'             => $_POST['year'],
    'transmission'     => $_POST['transmission'],
    'engine_spec'      => $_POST['engine_spec'] ?? null,
    'car_condition'    => $_POST['car_condition'] ?? null,
    'description'      => $_POST['description'] ?? null,
    'color'            => $_POST['color'] ?? null,
    'price'            => $_POST['price'],
    'on_sale'          => $_POST['on_sale'],
    'discount'         => $_POST['discount'],
    'lease_available'  => $_POST['lease_available'] ?? 'no',
    'lease_terms'      => $_POST['lease_terms'] ?? null,
    'status'           => $_POST['status'],
    'image_path'       => $image_path
];

// Normalize lease_available
$data['lease_available'] = strtolower(trim($data['lease_available'])) === 'yes' ? 1 : 0;

try {
    $controller->insertCar($data);
    echo json_encode(['success' => true, 'message' => 'Car added successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
exit;
