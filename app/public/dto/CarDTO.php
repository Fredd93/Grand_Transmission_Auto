<?php

class CarDTO
{
    private $carId;
    private $brand;
    private $model;
    private $price;
    private $onSale;
    private $discount;
    private $image_path;

    public function __construct($carId, $brand, $model, $price, $onSale, $discount, $image_path)
    {
        $this->carId = $carId;
        $this->brand = $brand;
        $this->model = $model;
        $this->price = $price;
        $this->onSale = $onSale;
        $this->discount = $discount;
        $this->image_path = $image_path;
    }

    // Getters
    public function getCarId() { return $this->carId; }
    public function getBrand() { return $this->brand; }
    public function getModel() { return $this->model; }
    public function getPrice() { return $this->price; }
    public function getOnSale() { return $this->onSale; }
    public function getDiscount() { return $this->discount; }
    public function getImage() { return $this->image_path; }
}
