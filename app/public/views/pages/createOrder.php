<?php
// app/public/views/pages/createOrder.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Get car ID from URL
$carId = $_GET['id'] ?? null;
if (!$carId) {
    echo "<p class='text-danger text-center mt-5'>Invalid car ID provided.</p>";
    exit;
}

// Include header partial
require_once __DIR__ . '/../partials/header.php';
?>

<div class="container mt-5">
  <h2 class="mb-4 text-center">Create Order for Car #<?= htmlspecialchars($carId) ?></h2>
  <form id="createOrderForm" class="mx-auto" style="max-width: 600px;">
    <input type="hidden" name="car_id" value="<?= htmlspecialchars($carId) ?>">

    <div class="mb-3">
      <label for="orderType" class="form-label">Order Type</label>
      <select id="orderType" name="order_type" class="form-select" required>
        <option value="purchase">Purchase</option>
        <option value="lease">Lease</option>
      </select>
    </div>

    

    <div class="mb-3">
      <label for="downPayment" class="form-label">Down Payment (&euro;)</label>
      <input type="number" class="form-control" id="downPayment" name="down_payment" required step="0.01">
    </div>

    <div class="mb-3">
      <label for="clientName" class="form-label">Client Name</label>
      <input type="text" class="form-control" id="clientName" name="client_name" required>
    </div>

    <div class="mb-3">
      <label for="clientEmail" class="form-label">Client Email</label>
      <input type="email" class="form-control" id="clientEmail" name="client_email" required>
    </div>

    <div class="mb-3">
      <label for="clientPhone" class="form-label">Client Phone</label>
      <input type="text" class="form-control" id="clientPhone" name="client_phone" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Submit Order</button>
  </form>
</div>

<script src="../../assets/js/createOrder.js"></script>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
