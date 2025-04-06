<?php
// Route for the homepage (accessible at "/" or "/Home")
Route::add('/', function () {
    $activePage = 'Home';
    require __DIR__ . '/../views/pages/index.php';
});
Route::add('/Home', function () {
    $activePage = 'Home';
    require __DIR__ . '/../views/pages/index.php';
});

// Route for login page
Route::add('/login', function () {
    $activePage = 'login';
    require __DIR__ . '/../views/pages/login.php';
});

// Route for employee home (for successful logins)
Route::add('/employeeHome', function () {
    $activePage = 'employeeHome';
    require __DIR__ . '/../views/pages/index.php';
});

// Route for cars page
Route::add('/cars', function () {
    $activePage = 'cars';
    require __DIR__ . '/../views/pages/cars.php';
});
// Route for cars page
Route::add('/car_details', function () {
    $activePage = 'details';
    include __DIR__ . '/../views/cars/details.php';

});
Route::add('/order', function () {

    // Check if the user is logged in and has the "employee" role
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'employee') {
        // Optionally redirect to login page or show 403 message
        header('Location: /login?unauthorized=true');
        exit;
    }

    $activePage = 'orders';
    require __DIR__ . '/../views/pages/orders.php';
});

Route::add('/car/([0-9]+)', function ($id) {
    $_GET['car_id'] = $id;
    include __DIR__ . '/../views/pages/carDetails.php';
});



// Additional page routes can be added here...
?>
