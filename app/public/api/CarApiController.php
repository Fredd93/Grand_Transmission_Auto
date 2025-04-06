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
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data || empty($data['brand']) || empty($data['model']) || empty($data['price'])) {
                ResponseHelper::sendError("Missing required fields", 400);
                return;
            }

            $success = $this->carModel->insertCar($data);
            if ($success) {
                ResponseHelper::sendJson(["message" => "Car added successfully"], 201);
            } else {
                ResponseHelper::sendError("Failed to add car", 500);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError("Internal Server Error", 500);
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
}
?>
