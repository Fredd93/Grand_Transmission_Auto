<?php
// app/public/api/login.php

// Start output buffering to catch stray output and avoid interfering with JSON output
ob_start();

// Log that this API file was reached
error_log("api/login.php reached");

// Set the response header to JSON
header('Content-Type: application/json');

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the UserModel
require_once __DIR__ . '/../models/UserModel.php';

$response = [
    'success'  => false,
    'message'  => '',
    'redirect' => ''
];

// Get username and password from POST data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Log the username for debugging (avoid logging passwords in production)
error_log("API login: username = " . $username);

if (empty($username) || empty($password)) {
    $response['message'] = 'Please fill in all required fields.';
    error_log("API login error: missing fields");
    $jsonResponse = json_encode($response);
    if ($jsonResponse === false) {
        error_log("JSON encode error (missing fields): " . json_last_error_msg());
        $jsonResponse = '{"success": false, "message": "Internal JSON encoding error."}';
    }
    echo $jsonResponse;
    ob_end_flush();
    exit;
}

// Create a new instance of UserModel and verify the user
$userModel = new UserModel();
$user = $userModel->verifyUser($username, $password);

if ($user) {
    error_log("API login: user found. Role: " . $user->getRole());
    // Allow only employees or managers to log in
    if (in_array($user->getRole(), ['employee', 'manager'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['role']      = $user->getRole();
        $_SESSION['username']  = $user->getUsername();

        $response['success']  = true;
        $response['redirect'] = '/employeeHome';
        error_log("API login: login successful, redirecting to /employeeHome");
    } else {
        $response['message'] = 'Access denied: Only employees and managers can log in.';
        error_log("API login: access denied for role: " . $user->getRole());
    }
} else {
    $response['message'] = 'Invalid username or password.';
    error_log("API login: user not verified for username: " . $username);
}

// Encode the response as JSON
$jsonResponse = json_encode($response);
if ($jsonResponse === false) {
    error_log("API login: JSON encode error: " . json_last_error_msg());
    $jsonResponse = '{"success": false, "message": "Internal JSON encoding error."}';
}

// Log the JSON response for debugging purposes
error_log("API login: Response JSON: " . $jsonResponse);

// Output the JSON and flush the buffer
echo $jsonResponse;
ob_end_flush();
exit;
?>
