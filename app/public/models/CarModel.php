<?php
require_once __DIR__ . '/../dto/CarDTO.php';
require_once __DIR__ . '/BaseModel.php';

class CarModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllCars()
    {
        $sql = "SELECT * FROM cars";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getFeaturedCars()
    {
        $sql = "SELECT * FROM cars ORDER BY RAND() LIMIT 5";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
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

    public function getDistinctBrands()
    {
        $sql = "SELECT DISTINCT brand FROM cars ORDER BY brand ASC";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
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
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getFilteredCars($filters)
    {
        $sql = "SELECT * FROM cars WHERE 1=1";
        $params = [];
    
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function insertCar($data)
    {
        $sql = "INSERT INTO cars (
            brand, model, year, transmission, engine_spec, car_condition, description, color,
            price, on_sale, discount, lease_available, lease_terms, status, image_path
        ) VALUES (
            :brand, :model, :year, :transmission, :engine_spec, :car_condition, :description, :color,
            :price, :on_sale, :discount, :lease_available, :lease_terms, :status, :image_path
        )";

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':brand', $data['brand']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':year', $data['year']);
        $stmt->bindParam(':transmission', $data['transmission']);
        $stmt->bindParam(':engine_spec', $data['engine_spec']);
        $stmt->bindParam(':car_condition', $data['car_condition']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':color', $data['color']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':on_sale', $data['on_sale']);
        $stmt->bindParam(':discount', $data['discount']);
        $stmt->bindParam(':lease_available', $data['lease_available']);
        $stmt->bindParam(':lease_terms', $data['lease_terms']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':image_path', $data['image_path']);
        $stmt->execute();

        return true;
    }
    public function getCarById($id)
    {
        $sql = "SELECT * FROM cars WHERE car_id = :id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $row : null;
    }
    
    public function editCarDetails($carId, $data)
    {
        $sql = "UPDATE cars SET 
                    brand = :brand,
                    model = :model,
                    year = :year,
                    transmission = :transmission,
                    engine_spec = :engine_spec,
                    car_condition = :car_condition,
                    description = :description,
                    color = :color,
                    price = :price,
                    on_sale = :on_sale,
                    discount = :discount,
                    lease_available = :lease_available,
                    lease_terms = :lease_terms,
                    status = :status,
                    image_path = :image_path
                WHERE car_id = :car_id";

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':brand', $data['brand']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':year', $data['year']);
        $stmt->bindParam(':transmission', $data['transmission']);
        $stmt->bindParam(':engine_spec', $data['engine_spec']);
        $stmt->bindParam(':car_condition', $data['car_condition']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':color', $data['color']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':on_sale', $data['on_sale']);
        $stmt->bindParam(':discount', $data['discount']);
        $stmt->bindParam(':lease_available', $data['lease_available']);
        $stmt->bindParam(':lease_terms', $data['lease_terms']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':image_path', $data['image_path']);
        $stmt->bindParam(':car_id', $carId);

        return $stmt->execute();
    }

}
