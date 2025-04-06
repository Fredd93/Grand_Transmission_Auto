<?php
include __DIR__ . '/../../partials/header.php';
$carId = $_GET['car_id'] ?? null;
?>

<section class="gt-container">
    <div id="car-details-container">
        <h2>Car Details</h2>
        <div id="car-details" class="card"></div>
        <div id="order-action" style="margin-top: 1rem;"></div>
    </div>
</section>

<script>
    const carId = <?php echo json_encode($carId); ?>;
</script>
<script src="../../assets/js/carDetails.js"></script>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
