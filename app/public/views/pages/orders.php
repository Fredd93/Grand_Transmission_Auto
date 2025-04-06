<?php
// employee_orders.php
require_once __DIR__ . '/../partials/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4 text-center">All Orders</h2>
    <div id="ordersContainer" class="row g-4">
        <!-- Orders will be injected here by JS -->
    </div>
</div>

<!-- Optional: Additional page-specific CSS -->
<link rel="stylesheet" href="/assets/css/employeeOrders.css">

<!-- Bootstrap JS + Orders JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/orders.js"></script>

</body>
</html>
