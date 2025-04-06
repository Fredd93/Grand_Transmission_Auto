<h1><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h1>
<img src="/assets/images/<?= htmlspecialchars($car['image_path']) ?>" alt="Car Image">
<p>Price: $<?= number_format($car['price'], 2) ?></p>
<p>Year: <?= htmlspecialchars($car['year']) ?></p>
<!-- Add more fields as needed -->
