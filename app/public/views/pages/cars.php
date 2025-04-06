<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include header partial (starts HTML, head, body)
include __DIR__ . '/../partials/header.php';

// Check if the logged-in user is employee or manager
$isEmployee = isset($_SESSION['role']) && in_array($_SESSION['role'], ['employee', 'manager']);
?>

<!-- Hero Section -->
<section class="heroCars">
  <div class="hero-overlay">
    <h1>A car selection fit for your taste</h1>
    <p>We host a wide range of cars for purchase and leasing</p>
  </div>
</section>

<!-- Filter Panel -->
<div class="filter-panel">
  <select id="brandSelect" class="form-select">
    <option value="">All Brands</option>
  </select>

  <select id="yearSelect" class="form-select">
    <option value="">All Years</option>
  </select>

  <select id="transmissionSelect" class="form-select">
    <option value="">All Transmissions</option>
  </select>

  <select id="onSaleSelect" class="form-select">
    <option value="">All Sale Status</option>
  </select>

  <input type="number" id="priceMin" class="form-control" placeholder="Min Price">
  <input type="number" id="priceMax" class="form-control" placeholder="Max Price">

  <button id="filterBtn" class="btn btn-primary">Filter</button>

  <?php if ($isEmployee): ?>
    <button id="addCarBtn" class="btn btn-success" style="margin-left: auto;">Add Car</button>
  <?php endif; ?>
</div>

<!-- Car Results -->
<div class="gt-container">
  <section id="all-cars">
    <h2>All Cars Available</h2>
    <div id="results" class="card-grid">
      <!-- Cards will be dynamically injected by carFilter.js -->
    </div>
  </section>
</div>

<!-- Add Car Modal Partial -->
<?php include __DIR__ . '/../partials/addCarModal.php'; ?>

<!-- Footer -->
<?php include __DIR__ . '/../partials/footer.php'; ?>

<!-- JavaScript Files -->
<script src="../../assets/js/carFilter.js"></script>
<script src="../../assets/js/addCar.js"></script>
<script src="../../assets/js/car.js"></script>
