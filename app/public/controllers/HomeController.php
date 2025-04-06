<?php
require_once __DIR__ . '/../models/CarModel.php';

class HomeController
{
    private $carModel;

    public function __construct()
    {
        // Instantiate CarModel; it automatically gets the PDO connection from BaseModel.
        $this->carModel = new CarModel();
    }

    public function index()
    {
        $featuredCars = $this->carModel->getFeaturedCars();
        $newArrivals  = $this->carModel->getNewArrivals();
        $dealsOfDay   = $this->carModel->getDealsOfDay();
        
        include __DIR__ . '/../views/partials/homepage_content.php';
    }
}
