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
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
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
}
?>
