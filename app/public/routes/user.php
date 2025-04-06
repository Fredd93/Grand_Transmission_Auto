<?php
// app/public/routes/user.php

require_once __DIR__ . "/../controllers/UserController.php";

// Route for listing all users
Route::add('/users', function () {
    $userController = new UserController();
    $users = $userController->getAllUsers();
    require_once __DIR__ . "/../views/pages/users.php";
});

// Route for a specific user (e.g., /user/2)
Route::add('/user/([0-9]+)', function ($userId) {
    $userController = new UserController();
    $user = $userController->get($userId);
    require_once __DIR__ . "/../views/pages/user.php";
});

// Route for logout
Route::add('/logout', function () {
    $userController = new UserController();
    $userController->logout();
});
?>
