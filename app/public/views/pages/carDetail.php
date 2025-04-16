<?php
// carDetails.php
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo "Invalid car ID.";
    exit;
}

include __DIR__ . '/../partials/header.php';
?>

<div class="container mt-5">
  <div id="carDetails" class="card shadow-lg p-4">
    <h2 class="text-center mb-4">Car Details</h2>
    <div id="carContent">Loading car details...</div>
    <div class="text-center mt-4">
      <a href="/order/create/<?php echo htmlspecialchars($id); ?>" class="btn btn-success">Create Order for this Car</a>
    </div>
  </div>
</div>

<script>
  const carId = <?php echo json_encode($id); ?>;
</script>
<script src="../../assets/js/carDetails.js"></script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
