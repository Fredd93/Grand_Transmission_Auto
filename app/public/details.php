<?php
require_once __DIR__ . '/controllers/CarController.php';

$controller = new CarController();

if (isset($_GET['id'])) {
    $controller->showDetailsPage($_GET['id']);
} else {
    echo "No car ID provided.";
}
