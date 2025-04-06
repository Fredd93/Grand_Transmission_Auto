<?php
require_once __DIR__ . '/../dto/CarDTO.php';
require_once __DIR__ . '/BaseModel.php';

class CarModel extends BaseModel
{
    public function __construct()
    {
        // Call BaseModel constructor to initialize the PDO connection.
        parent::__construct();
    }

    public function getAllCars()
    {
        $sql = "SELECT * FROM cars";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();

        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new CarDTO(
                $row['car_id'],
                $row['brand'],
                $row['model'],
                $row['price'],
                $row['on_sale'],
                $row['discount'],
                $row['image_path']
            );
        }
        return $results;
    }

    // Fetches 5 random cars as featured cars
    public function getFeaturedCars()
    {
        $sql = "SELECT * FROM cars ORDER BY RAND() LIMIT 5";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new CarDTO(
                $row['car_id'],
                $row['brand'],
                $row['model'],
                $row['price'],
                $row['on_sale'],
                $row['discount'],
                $row['image_path']
            );
        }
        return $results;
    }

    // Fetches the 5 most recent cars based on the created_at attribute
        public function getNewArrivals()
    {
        $sql = "SELECT * FROM cars WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY created_at DESC LIMIT 5";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new CarDTO(
                $row['car_id'],
                $row['brand'],
                $row['model'],
                $row['price'],
                $row['on_sale'],
                $row['discount'],
                $row['image_path']
            );
        }
        return $results;
    }


    // Fetches all cars that are on sale
    public function getDealsOfDay()
    {
        $sql = "SELECT * FROM cars WHERE on_sale = 'yes'";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new CarDTO(
                $row['car_id'],
                $row['brand'],
                $row['model'],
                $row['price'],
                $row['on_sale'],
                $row['discount'],
                $row['image_path']
            );
        }
        return $results;
    }
    // In CarModel.php

public function getDistinctBrands()
{
    $sql = "SELECT DISTINCT brand FROM cars ORDER BY brand ASC";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN); 
    // returns an array of brand strings
}

public function getDistinctYears()
{
    $sql = "SELECT DISTINCT year FROM cars ORDER BY year DESC";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

public function getDistinctTransmissions()
{
    $sql = "SELECT DISTINCT transmission FROM cars ORDER BY transmission ASC";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Optionally, get on_sale distinct values (if it's just 'yes' or 'no', you could hardcode)
public function getDistinctOnSaleValues()
{
    $sql = "SELECT DISTINCT on_sale FROM cars ORDER BY on_sale ASC";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

public function getPriceBounds()
{
    $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM cars";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // e.g. ['min_price' => 1000, 'max_price' => 50000]
}

public function getFilteredCars($filters)
{
    // $filters is an associative array, e.g.:
    // [
    //   'brand' => 'Toyota',
    //   'year' => 2020,
    //   'transmission' => 'automatic',
    //   'on_sale' => 'yes',
    //   'price_min' => 10000,
    //   'price_max' => 30000
    // ]

    // Start building the query
    $sql = "SELECT * FROM cars WHERE 1=1";
    $params = [];

    // Conditionally add filters
    if (!empty($filters['brand'])) {
        $sql .= " AND brand = :brand";
        $params[':brand'] = $filters['brand'];
    }

    if (!empty($filters['year'])) {
        $sql .= " AND year = :year";
        $params[':year'] = $filters['year'];
    }

    if (!empty($filters['transmission'])) {
        $sql .= " AND transmission = :transmission";
        $params[':transmission'] = $filters['transmission'];
    }

    if (!empty($filters['on_sale'])) {
        $sql .= " AND on_sale = :on_sale";
        $params[':on_sale'] = $filters['on_sale'];
    }

    // Price range
    if (!empty($filters['price_min'])) {
        $sql .= " AND price >= :price_min";
        $params[':price_min'] = $filters['price_min'];
    }

    if (!empty($filters['price_max'])) {
        $sql .= " AND price <= :price_max";
        $params[':price_max'] = $filters['price_max'];
    }

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($params);

    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = new CarDTO(
            $row['car_id'],
            $row['brand'],
            $row['model'],
            $row['price'],
            $row['on_sale'],
            $row['discount'],
            $row['image_path']
        );
        // Add additional fields if needed (e.g. year, transmission, etc.)
    }
    return $results;
}
}
