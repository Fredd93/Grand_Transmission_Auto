<?php
require_once __DIR__ . '/../models/CarModel.php';
require_once __DIR__ . '/../api/utils/ResponseHelper.php';

class CarApiController
{
    private $carModel;

    public function __construct()
    {
        $this->carModel = new CarModel();
    }

    // GET: /api/cars
    public function getAllCars()
    {
        try {
            $cars = $this->carModel->getAllCars();
            ResponseHelper::sendJson($cars);
        } catch (Exception $e) {
            ResponseHelper::sendError("Failed to fetch cars", 500);
        }
    }

    // GET: /api/cars/featured
    public function getFeaturedCars()
    {
        try {
            $cars = $this->carModel->getFeaturedCars();
            ResponseHelper::sendJson($cars);
        } catch (Exception $e) {
            ResponseHelper::sendError("Failed to fetch featured cars", 500);
        }
    }

    // GET: /api/cars/new
    public function getNewArrivals()
    {
        try {
            $cars = $this->carModel->getNewArrivals();
            ResponseHelper::sendJson($cars);
        } catch (Exception $e) {
            ResponseHelper::sendError("Failed to fetch new arrivals", 500);
        }
    }

    // GET: /api/cars/deals
    public function getDealsOfDay()
    {
        try {
            $cars = $this->carModel->getDealsOfDay();
            ResponseHelper::sendJson($cars);
        } catch (Exception $e) {
            ResponseHelper::sendError("Failed to fetch deals", 500);
        }
    }

    // GET: /api/cars/filters
    public function getFilterOptions()
    {
        try {
            $response = [
                'brands'       => $this->carModel->getDistinctBrands(),
                'years'        => $this->carModel->getDistinctYears(),
                'transmissions'=> $this->carModel->getDistinctTransmissions(),
                'on_sale'      => $this->carModel->getDistinctOnSaleValues(),
                'price_bounds' => $this->carModel->getPriceBounds()
            ];
            ResponseHelper::sendJson($response);
        } catch (Exception $e) {
            ResponseHelper::sendError("Failed to load filter options", 500);
        }
    }

    // POST: /api/cars/filter
    public function getFilteredCars()
    {
        try {
            $filters = json_decode(file_get_contents("php://input"), true);
            $cars = $this->carModel->getFilteredCars($filters);
            ResponseHelper::sendJson($cars);
        } catch (Exception $e) {
            ResponseHelper::sendError("Failed to apply filters", 500);
        }
    }

    // POST: /api/cars
    public function insertCar()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                ResponseHelper::sendError("Invalid request method", 405);
                return;
            }

            // Validate form fields
            $requiredFields = ['brand', 'model', 'year', 'transmission', 'price', 'on_sale', 'discount', 'status'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    ResponseHelper::sendError("Missing required field: $field", 400);
                    return;
                }
            }

            // ✅ Validate image
            if (!isset($_FILES['image_path']) || $_FILES['image_path']['error'] !== UPLOAD_ERR_OK) {
                ResponseHelper::sendError("Image upload failed or missing", 400);
                return;
            }

            $originalName = basename($_FILES['image_path']['name']);
            $cleanName = preg_replace("/[^a-zA-Z0-9-_\.]/", "_", $originalName);
            $uniqueName = uniqid('car_', true) . '_' . $cleanName;
                    
            // ✅ Declare early so it's always available
            $relativePath = '../../assets/images/' . $uniqueName;
            $uploadPath = __DIR__ . '/../../public/assets/images/' . $uniqueName;
                    
            if (!move_uploaded_file($_FILES['image_path']['tmp_name'], $uploadPath)) {
                ResponseHelper::sendError("Failed to move uploaded file.", 500);
                return;
            }


            // Prepare data
            $data = [
                'brand'           => $_POST['brand'],
                'model'           => $_POST['model'],
                'year'            => $_POST['year'],
                'transmission'    => $_POST['transmission'],
                'engine_spec'     => $_POST['engine_spec'] ?? '',
                'car_condition'   => $_POST['car_condition'] ?? '',
                'description'     => $_POST['description'] ?? '',
                'color'           => $_POST['color'] ?? '',
                'price'           => $_POST['price'],
                'on_sale'         => $_POST['on_sale'],
                'discount'        => $_POST['discount'],
                'lease_available' => isset($_POST['lease_available']) && $_POST['lease_available'] === 'yes' ? 1 : 0,
                'lease_terms'     => $_POST['lease_terms'] ?? '',
                'status'          => $_POST['status'],
                'image_path'      => $relativePath
            ];

            // Insert car
            $success = $this->carModel->insertCar($data);

            if ($success) {
                ResponseHelper::sendJson(['success' => true, 'message' => 'Car added successfully']);
            } else {
                ResponseHelper::sendError("Failed to insert car", 500);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError("Server error: " . $e->getMessage(), 500);
        }
    }



    // GET: /api/cars/{id}
    public function getCarById($id)
    {
        try {
            $car = $this->carModel->getCarById($id);
            if ($car) {
                ResponseHelper::sendJson($car);
            } else {
                ResponseHelper::sendError("Car not found", 404);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError("Failed to fetch car", 500);
        }
    }
    public function updateCarDetails()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                ResponseHelper::sendError("Invalid request method", 405);
                return;
            }
        
            if (!isset($_POST['car_id'])) {
                ResponseHelper::sendError("Missing car_id", 400);
                return;
            }
        
            $carId = $_POST['car_id'];
        
            $originalName = basename($_FILES['image_path']['name'] ?? '');
            $cleanName = preg_replace("/[^a-zA-Z0-9-_\.]/", "_", $originalName);
            $uniqueName = uniqid('car_', true) . '_' . $cleanName;
            $relativePath = '../../assets/images/' . $uniqueName;
            $uploadPath = __DIR__ . '/../../public/assets/images/' . $uniqueName;
        
            if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
                move_uploaded_file($_FILES['image_path']['tmp_name'], $uploadPath);
            } else {
                $relativePath = $_POST['existing_image_path'] ?? ''; // fallback to existing image
            }
        
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
                'on_sale' => $_POST['on_sale'] === 'yes' ? 1 : 0,
                'discount' => $_POST['discount'],
                'lease_available' => $_POST['lease_available'] === 'yes' ? 1 : 0,
                'lease_terms' => $_POST['lease_terms'] ?? '',
                'status' => $_POST['status'],
                'image_path' => $relativePath
            ];
        
            $success = $this->carModel->editCarDetails($carId, $data);
        
            if ($success) {
                ResponseHelper::sendJson(["success" => true, "message" => "Car updated successfully"]);
            } else {
                ResponseHelper::sendError("Failed to update car", 500);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError("Server error: " . $e->getMessage(), 500);
        }
    }

}
?>
