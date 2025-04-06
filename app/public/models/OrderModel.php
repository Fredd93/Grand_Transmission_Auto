<?php
require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    // ✅ Fetch all orders as associative arrays
    public function getAllOrders()
    {
        $sql = "
            SELECT 
                o.*, 
                c.brand AS car_brand, 
                c.model AS car_model
            FROM orders o
            JOIN cars c ON o.car_id = c.car_id
            ORDER BY o.created_at DESC
        ";
        
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ✅ Change order status
    public function updateOrderStatus($orderId, $newStatus)
    {
        $sql = "UPDATE orders SET status = :status WHERE order_id = :order_id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // ✅ Get a specific order by ID as an associative array
    public function getOrderById($orderId)
    {
        $sql = "SELECT * FROM orders WHERE order_id = :order_id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); // return as assoc array or null
    }
    public function createOrder($data)
    {
        $sql = "INSERT INTO orders (
            car_id, order_type, status, down_payment,
            client_name, client_email, client_phone, employee_id,
            created_at, updated_at
        ) VALUES (
            :car_id, :order_type, :status, :down_payment,
            :client_name, :client_email, :client_phone, :employee_id,
            NOW(), NOW()
        )";

        $stmt = self::$pdo->prepare($sql);

        $stmt->bindParam(':car_id', $data['car_id'], PDO::PARAM_INT);
        $stmt->bindParam(':order_type', $data['order_type']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':down_payment', $data['down_payment']);
        $stmt->bindParam(':client_name', $data['client_name']);
        $stmt->bindParam(':client_email', $data['client_email']);
        $stmt->bindParam(':client_phone', $data['client_phone']);
        $stmt->bindParam(':employee_id', $data['employee_id'], PDO::PARAM_INT);

        return $stmt->execute();
    }

}
?>
