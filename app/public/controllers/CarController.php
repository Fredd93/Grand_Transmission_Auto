<?php
// app/controllers/CarController.php

require_once __DIR__ . '/../models/CarModel.php';
require_once __DIR__ . '/../dto/CarDTO.php';

class CarController
{
    private $carModel;

    public function __construct()
    {
        $this->carModel = new CarModel();
    }

    public function getAllCars()
    {
        return $this->carModel->getAllCars();
    }

    public function getFeaturedCars()
    {
        return $this->carModel->getFeaturedCars();
    }

    public function getNewArrivals()
    {
        return $this->carModel->getNewArrivals();
    }

    public function getDealsOfDay()
    {
        return $this->carModel->getDealsOfDay();
    }

    public function getDistinctBrands()
    {
        return $this->carModel->getDistinctBrands();
    }

    public function getDistinctYears()
    {
        return $this->carModel->getDistinctYears();
    }

    public function getDistinctTransmissions()
    {
        return $this->carModel->getDistinctTransmissions();
    }

    public function getDistinctOnSaleValues()
    {
        return $this->carModel->getDistinctOnSaleValues();
    }

    public function getPriceBounds()
    {
        return $this->carModel->getPriceBounds();
    }

    public function getFilteredCars($filters)
    {
        return $this->carModel->getFilteredCars($filters);
    }

    public function insertCar($data)
    {
        return $this->carModel->insertCar($data);
    }

    public function getCarById($id)
    {
        return $this->carModel->getCarById($id);
    }
}
