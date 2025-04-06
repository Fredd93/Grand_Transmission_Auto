<?php
require_once(__DIR__ . '/../models/CarModel.php');
require_once(__DIR__ . '/../api/utils/ResponseHelper.php');

class CarApiController {
    private $model;

    public function __construct() {
        $this->model = new CarModel();
    }

    public function getAllCars() {
        try {
            $cars = $this->model->getAllCars();
            ResponseHelper::sendJson($cars);
        } catch (Exception $e) {
            ResponseHelper::sendError('Failed to fetch cars: ' . $e->getMessage(), 500);
        }
    }

    public function getCarById($id) {
        try {
            $car = $this->model->getCarById($id);
            if ($car) {
                ResponseHelper::sendJson($car);
            } else {
                ResponseHelper::sendError('Car not found', 404);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError('Failed to fetch car: ' . $e->getMessage(), 500);
        }
    }

    public function getNewArrivals() {
        try {
            $cars = $this->model->getNewArrivals();
            ResponseHelper::sendJson($cars);
        } catch (Exception $e) {
            ResponseHelper::sendError("Failed to fetch new arrivals", 500);
        }
    }

    public function getDealsOfDay() {
        try {
            $cars = $this->model->getDealsOfDay();
            ResponseHelper::sendJson($cars);
        } catch (Exception $e) {
            ResponseHelper::sendError("Failed to fetch deals", 500);
        }
    }

    public function getFilterOptions() {
        try {
            $data = [
                'brands' => $this->model->getDistinctBrands(),
                'years' => $this->model->getDistinctYears(),
                'transmissions' => $this->model->getDistinctTransmissions(),
                'on_sale_values' => $this->model->getDistinctOnSaleValues(),
                'price_bounds' => $this->model->getPriceBounds()
            ];
            ResponseHelper::sendJson($data);
        } catch (Exception $e) {
            ResponseHelper::sendError('Failed to load filters: ' . $e->getMessage(), 500);
        }
    }

    public function getFilteredCars() {
        try {
            $filters = json_decode(file_get_contents("php://input"), true);
            $results = $this->model->getFilteredCars($filters);
            ResponseHelper::sendJson($results);
        } catch (Exception $e) {
            ResponseHelper::sendError('Failed to filter cars: ' . $e->getMessage(), 500);
        }
    }

    public function insertCar() {
        try {
            // Validate required fields
            $required = ['brand', 'model', 'year', 'transmission', 'price', 'on_sale', 'discount', 'status'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    ResponseHelper::sendError("Field '$field' is required.", 400);
                    return;
                }
            }

            if (!isset($_FILES['image_path']) || $_FILES['image_path']['error'] !== UPLOAD_ERR_OK) {
                ResponseHelper::sendError("An image file is required.", 400);
                return;
            }

            // Save the uploaded image
            $uploadDir = __DIR__ . '/../assets/images/';
            $uploadedFile = $_FILES['image_path'];
            $filename = uniqid() . '_' . basename($uploadedFile['name']);
            $targetFile = $uploadDir . $filename;

            if (!move_uploaded_file($uploadedFile['tmp_name'], $targetFile)) {
                ResponseHelper::sendError("Failed to move uploaded file.", 500);
                return;
            }

            $image_path = "../../assets/images/" . $filename;

            // Gather form data
            $data = [
                'brand' => $_POST['brand'],
                'model' => $_POST['model'],
                'year' => $_POST['year'],
                'transmission' => $_POST['transmission'],
                'engine_spec' => $_POST['engine_spec'] ?? '',
                'car_condition' => $_POST['car_condition'] ?? '',
                'description' => $_POST['description'] ?? '',
                'color' => $_POST['color'] ?? '',
                'price' => $_POST['price'],
                'on_sale' => $_POST['on_sale'],
                'discount' => $_POST['discount'],
                'lease_available' => isset($_POST['lease_available']) && $_POST['lease_available'] === 'yes' ? 1 : 0,
                'lease_terms' => $_POST['lease_terms'] ?? '',
                'status' => $_POST['status'],
                'image_path' => $image_path
            ];

            $success = $this->model->insertCar($data);

            if ($success) {
                ResponseHelper::sendJson(['message' => 'Car added successfully']);
            } else {
                ResponseHelper::sendError("Failed to insert car", 500);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError("Server error: " . $e->getMessage(), 500);
        }
    }
    public function getFeaturedCars() {
        try {
            $cars = $this->model->getFeaturedCars();
            ResponseHelper::sendJson($cars);
        } catch (Exception $e) {
            ResponseHelper::sendError('Failed to fetch featured cars: ' . $e->getMessage(), 500);
        }
    }
}