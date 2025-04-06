<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/CarModel.php';
$carModel = new CarModel();
// Initially load all cars (before filtering)
$cars = $carModel->getAllCars();

// Include header partial (it includes the <head> and starts the <body>)
include __DIR__ . '/../partials/header.php';

// Check if an employee (or manager) is logged in
$isEmployee = isset($_SESSION['role']) && in_array($_SESSION['role'], ['employee', 'manager']);
?>
<section class="heroCars">
  <div class="hero-overlay">
    <h1>A car selection fit for your taste</h1>
    <p>We host a wide range of cars for purchase and leasing</p>
  </div>
</section>
<div class="gt-container">
  <!-- Filter Panel -->
  <div class="filter-panel">
    <select id="brandSelect">
      <option value="">All Brands</option>
    </select>
    <select id="yearSelect">
      <option value="">All Years</option>
    </select>
    <select id="transmissionSelect">
      <option value="">All Transmissions</option>
    </select>
    <select id="onSaleSelect">
      <option value="">All Sale Status</option>
    </select>
    <input type="number" id="priceMin" placeholder="Min Price">
    <input type="number" id="priceMax" placeholder="Max Price">
    <button id="filterBtn" class="btn btn-primary">Filter</button>
    <?php if ($isEmployee): ?>
      <!-- Add Car button for employees -->
      <button id="addCarBtn" class="btn btn-success" style="margin-left: auto;">Add Car</button>
    <?php endif; ?>
  </div>

  <!-- Results Container -->
  <section id="all-cars">
    <h2>All Cars Available</h2>
    <div id="results" class="card-grid">
      <?php foreach ($cars as $car): ?>
        <div class="card" data-car-id="<?php echo $car->getCarId(); ?>">
          <?php if ($isEmployee): ?>
            <!-- Three-dot menu for Edit / Remove -->
            <div class="card-options" style="position: absolute; top: 0.5rem; right: 0.5rem;">
              <button class="options-btn btn btn-link" onclick="toggleOptionsMenu(event, <?php echo $car->getCarId(); ?>)">
                &#x22EE;
              </button>
              <div class="options-menu" id="options-menu-<?php echo $car->getCarId(); ?>" style="display: none; position: absolute; right: 0; background: #fff; border: 1px solid #ccc; border-radius: 4px; z-index: 100;">
                <a href="#" class="dropdown-item edit-car" data-car-id="<?php echo $car->getCarId(); ?>">Edit</a>
                <a href="#" class="dropdown-item delete-car" data-car-id="<?php echo $car->getCarId(); ?>">Remove</a>
              </div>
            </div>
          <?php endif; ?>
          <img src="/assets/images/<?php echo htmlspecialchars($car->getImage()); ?>" 
               class="card-img-top" 
               alt="<?php echo htmlspecialchars($car->getBrand() . ' ' . $car->getModel()); ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($car->getBrand() . ' ' . $car->getModel()); ?></h5>
            <?php if ($car->getOnSale() === 'yes' && $car->getDiscount() > 0): 
                    $oldPrice = $car->getPrice();
                    $discount = $car->getDiscount();
                    $newPrice = $oldPrice * (1 - $discount / 100);
            ?>
              <p class="card-text">
                Price: <del>$<?php echo number_format($oldPrice, 2); ?></del>
                <span class="text-success">$<?php echo number_format($newPrice, 2); ?></span>
              </p>
              <p class="text-danger">On Sale: <?php echo htmlspecialchars($discount); ?>% off</p>
            <?php else: ?>
              <p class="card-text">Price: $<?php echo number_format($car->getPrice(), 2); ?></p>
            <?php endif; ?>
            <a href="/details.php?id=<?php echo urlencode($car->getCarId()); ?>" class="btn btn-outline-accent">View Details</a>
            <a href="/purchase.php?id=<?php echo urlencode($car->getCarId()); ?>" class="btn btn-outline-accent">Purchase Options</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

<?php
// Include the footer partial (which closes the <body> and </html> tags)
include __DIR__ . '/../partials/footer.php';
?>

<!-- Include the dedicated car filtering JavaScript -->
<script src="/assets/js/carFilter.js"></script>
<script src="/assets/js/addCar.js"></script>


<!-- Inline JavaScript for handling employee actions -->
<script>
  // Toggle display of the options menu for a car
  function toggleOptionsMenu(event, carId) {
    event.stopPropagation();
    const menu = document.getElementById('options-menu-' + carId);
    // Hide any other open menus
    document.querySelectorAll('.options-menu').forEach(m => {
      if (m !== menu) m.style.display = 'none';
    });
    menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
  }

  // Hide open menus when clicking outside
  document.addEventListener('click', function() {
    document.querySelectorAll('.options-menu').forEach(menu => {
      menu.style.display = 'none';
    });
  });

  // Event delegation for Edit and Delete options
  document.addEventListener('click', function(e) {
    if (e.target.matches('.edit-car')) {
      e.preventDefault();
      const carId = e.target.getAttribute('data-car-id');
      // Trigger your edit logic (for example, open an edit modal)
      console.log("Edit car", carId);
      // TODO: Implement edit functionality (e.g., open modal with a form pre-filled with car data)
    } else if (e.target.matches('.delete-car')) {
      e.preventDefault();
      const carId = e.target.getAttribute('data-car-id');
      if (confirm("Are you sure you want to remove this car?")) {
        // Trigger your delete logic via AJAX
        fetch('/api/delete_car?car_id=' + encodeURIComponent(carId))
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Remove the card from the DOM
              const card = document.querySelector(`.card[data-car-id="${carId}"]`);
              if (card) card.remove();
            } else {
              alert("Failed to remove car: " + data.message);
            }
          })
          .catch(error => console.error("Error deleting car:", error));
      }
    }
  });

  // Handle "Add Car" button click (if present)
  <?php if ($isEmployee): ?>
    document.getElementById('addCarBtn').addEventListener('click', function() {
      // Trigger your add car logic (for example, open an add car modal)
      console.log("Add new car");
      // TODO: Implement add car functionality (e.g., open modal with blank form)
    });
  <?php endif; ?>
</script>
