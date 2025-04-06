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

// Additional page routes can be added here...
?>
