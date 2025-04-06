<?php
// app/public/api/add_car.php

header('Content-Type: application/json');

require_once __DIR__ . '/../../models/CarModel.php';

$carModel = new CarModel();

// Check required POST fields (adjust these names to match your table)
$required = ['brand', 'model', 'price', 'on_sale', 'discount', 'image_path'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Field '$field' is required."]);
        exit;
    }
}

$brand      = $_POST['brand'];
$model      = $_POST['model'];
$price      = $_POST['price'];
$on_sale    = $_POST['on_sale'];
$discount   = $_POST['discount'];
$image_path = $_POST['image_path'];

try {
    // Build an insert query. Adjust the table and columns if needed.
    $sql = "INSERT INTO cars (brand, model, price, on_sale, discount, image_path) 
            VALUES (:brand, :model, :price, :on_sale, :discount, :image_path)";
    // Use the getter method to access the PDO connection.
    $stmt = $carModel->getPDO()->prepare($sql);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':model', $model);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':on_sale', $on_sale);
    $stmt->bindParam(':discount', $discount);
    $stmt->bindParam(':image_path', $image_path);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Car added successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
exit;
?>
