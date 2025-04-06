<?php
require_once __DIR__ . '/../utils/ResponseHelper.php';
require_once __DIR__ . '/../../models/UserModel.php';

class AuthApiController {
    public function login() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Missing credentials']);
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->verifyUser($username, $password);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
            return;
        }

        if (!in_array($user->getRole(), ['employee', 'manager'])) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        $_SESSION['logged_in'] = true;
        $_SESSION['username']  = $user->getUsername();
        $_SESSION['role']      = $user->getRole();

        echo json_encode(['success' => true, 'redirect' => '/employeeHome']);
    }

    public function logout() {
        session_start();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
}
