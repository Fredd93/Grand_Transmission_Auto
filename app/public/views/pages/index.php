<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Force the active page to be 'Home'
$activePage = 'Home';

// Include HomeController from the controllers folder
require_once __DIR__ . '/../../controllers/HomeController.php';

// Optionally include common partials for header and navbar
include __DIR__ . '/../partials/header.php';

// Instantiate HomeController (it will set up CarModel and use BaseModel's PDO)
$homeController = new HomeController();
$homeController->index();

// Include the footer after the view
include __DIR__ . '/../partials/footer.php';
?>
