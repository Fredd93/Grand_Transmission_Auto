<?php
require_once(__DIR__ . '/../models/OrderModel.php');
require_once(__DIR__ . '/../api/utils/ResponseHelper.php');
require_once __DIR__ . '/utils/MailService.php';


class OrderApiController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    /**
     * Get all orders
     */
    public function getAllOrders() {
        try {
            $orders = $this->orderModel->getAllOrders();
            if (!empty($orders)) {
                ResponseHelper::sendJson($orders);
            } else {
                ResponseHelper::sendError('No orders found', 404);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError('Failed to fetch orders: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get a specific order by ID
     */
    public function getOrderById($orderId) {
        try {
            $order = $this->orderModel->getOrderById($orderId);
            if ($order) {
                ResponseHelper::sendJson($order);
            } else {
                ResponseHelper::sendError('Order not found', 404);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError('Failed to fetch order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update order status
     */
    public function updateOrderStatus()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['order_id'], $data['status'])) {
                ResponseHelper::sendError('Missing order_id or status', 400);
                return;
            }

            $orderId = (int)$data['order_id'];
            $newStatus = $data['status'];

            // Fetch full order before updating
            $order = $this->orderModel->getOrderById($orderId);
            if (!$order) {
                ResponseHelper::sendError('Order not found', 404);
                return;
            }

            $success = $this->orderModel->updateOrderStatus($orderId, $newStatus);

            if ($success) {
                // ✅ Send email after successful update
                MailService::sendStatusEmail(
                    $order['client_email'],
                    $order['client_name'],
                    $orderId,
                    $newStatus
                );

                ResponseHelper::sendJson(['message' => 'Order status updated and email sent']);
            } else {
                ResponseHelper::sendError('Failed to update status', 500);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError('Internal Server Error: ' . $e->getMessage(), 500);
        }
    }

    public function createOrder()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
        
            // Force status to "pending"
            $data['status'] = 'pending';
        
            // Required fields (remove 'status' from validation)
            $required = ['car_id', 'order_type', 'down_payment', 'client_name', 'client_email', 'client_phone'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    ResponseHelper::sendError("Missing required field: $field", 400);
                    return;
                }
            }
        
            // Optional employee_id (e.g., for logged-in staff)
            $data['employee_id'] = $_SESSION['user_id'] ?? null;
        
            $success = $this->orderModel->createOrder($data);
        
            if ($success) {
                ResponseHelper::sendJson(['success' => true, 'message' => 'Order created successfully.'], 201);
            } else {
                ResponseHelper::sendError('Failed to create order', 500);
            }
        } catch (Exception $e) {
            ResponseHelper::sendError("Internal Server Error: " . $e->getMessage(), 500);
        }
    }


}
?>
