<?php
require_once __DIR__ . '/../models/CarModel.php';

class CarController
{
    private $carModel;

    public function __construct()
    {
        $this->carModel = new CarModel();
    }

    public function showDetailsPage($id)
    {
        if (!is_numeric($id)) {
            echo "Invalid car ID.";
            return;
        }

        $car = $this->carModel->getCarById($id);

        if (!$car) {
            echo "Car not found.";
            return;
        }

        $carData = $car;

        include __DIR__ . '/../views/cars/carDetails.php';
    }
}
