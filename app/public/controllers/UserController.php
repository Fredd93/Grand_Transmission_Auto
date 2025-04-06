<?php
// app/public/controllers/UserController.php

require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        // Initialize the user model, which automatically sets up the PDO connection via BaseModel.
        $this->userModel = new UserModel();
    }

    /**
     * Attempt to log in a user using the provided username and password.
     * Only users with the role 'employee' or 'manager' are allowed.
     *
     * @param string $username
     * @param string $password
     * @return string Empty on success; error message on failure.
     */
    public function login($username, $password)
    {
        $user = $this->userModel->verifyUser($username, $password);
        if ($user) {
            // Ensure the user is an employee or manager
            if (!in_array($user->getRole(), ['employee', 'manager'])) {
                return "Access denied: Only employees and managers can log in.";
            }

            // Set session data upon successful login
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = $user->getRole();
            $_SESSION['username'] = $user->getUsername();

            // Redirect to employee home (which, in Option 1, is the same as Home with conditional content)
            header("Location: /employeeHome");
            exit;
        } else {
            // If verification fails, return an error message
            return "Invalid username or password.";
        }
    }

    /**
     * Fetch all users (if needed).
     *
     * @return array An array of UserDTO objects.
     */
    public function getAllUsers()
    {
        return $this->userModel->getAllUsers();
    }

    /**
     * Fetch a single user by their ID.
     *
     * @param int $userId
     * @return UserDTO|null
     */
    public function get($userId)
    {
        return $this->userModel->getUserById($userId);
    }

    /**
     * Log out the current user.
     */
    public function logout()
    {
        session_destroy();
        header("Location: /Home");
        exit;
    }
}
?>
