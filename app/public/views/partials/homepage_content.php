<section class="hero">
  <div class="hero-overlay">
    <h1>Elevate Your Driving Experience</h1>
    <p>Grand Transmissions Auto helps you find the perfect vehicle. Discover our premium selection and drive away with confidence.</p>
  </div>
</section>
<div class="gt-container">
  <!-- Featured Cars Section -->
  <section id="featured-cars">
    <h2>Featured Cars</h2>
    <div class="card-grid">
      <?php foreach ($featuredCars as $car): ?>
        <div class="card">
          <img src="assets/images/<?php echo htmlspecialchars($car->getImage()); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($car->getBrand() . ' ' . $car->getModel()); ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($car->getBrand() . ' ' . $car->getModel()); ?></h5>
            <p class="card-text">Price: $<?php echo htmlspecialchars($car->getPrice()); ?></p>
            <?php if ($car->getOnSale() === 'yes'): ?>
              <p class="text-danger">On Sale: <?php echo htmlspecialchars($car->getDiscount()); ?>% off</p>
            <?php endif; ?>
            <a href="details.php?id=<?php echo urlencode($car->getCarId()); ?>" class="btn btn-outline-accent">View Details</a>
            <a href="purchase.php?id=<?php echo urlencode($car->getCarId()); ?>" class="btn btn-outline-accent">Purchase Options</a>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- New Arrivals Section -->
  <section id="new-arrivals">
    <h2>New Arrivals</h2>
    <div class="card-grid">
      <?php foreach ($newArrivals as $car): ?>
        <div class="card">
          <img src="assets/images/<?php echo htmlspecialchars($car->getImage()); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($car->getBrand() . ' ' . $car->getModel()); ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($car->getBrand() . ' ' . $car->getModel()); ?></h5>
            <p class="card-text">Price: $<?php echo htmlspecialchars($car->getPrice()); ?></p>
            <?php if ($car->getOnSale() === 'yes'): ?>
              <p class="text-danger">On Sale: <?php echo htmlspecialchars($car->getDiscount()); ?>% off</p>
            <?php endif; ?>
            <a href="details.php?id=<?php echo urlencode($car->getCarId()); ?>" class="btn btn-outline-accent">View Details</a>
            <a href="purchase.php?id=<?php echo urlencode($car->getCarId()); ?>" class="btn btn-outline-accent">Purchase Options</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Deals of the Day Section -->
  <section id="deals-of-day">
    <h2>Deals of the Day</h2>
    <div class="card-grid">
      <?php foreach ($dealsOfDay as $car): ?>
        <div class="card">
          <img src="assets/images/<?php echo htmlspecialchars($car->getImage()); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($car->getBrand() . ' ' . $car->getModel()); ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($car->getBrand() . ' ' . $car->getModel()); ?></h5>
            <?php 
              $oldPrice = $car->getPrice();
              $discount = $car->getDiscount();
              if ($car->getOnSale() === 'yes' && $discount > 0) {
                  $newPrice = $oldPrice * (1 - $discount / 100);
            ?>
              <p class="card-text">
                Price: <del>$<?php echo number_format($oldPrice, 2); ?></del>
                <span class="text-success">$<?php echo number_format($newPrice, 2); ?></span>
              </p>
            <?php } else { ?>
              <p class="card-text">Price: $<?php echo number_format($oldPrice, 2); ?></p>
            <?php } ?>
            <p class="text-danger">On Sale: <?php echo htmlspecialchars($discount); ?>% off</p>
            <a href="details.php?id=<?php echo urlencode($car->getCarId()); ?>" class="btn btn-outline-accent">View Details</a>
            <a href="purchase.php?id=<?php echo urlencode($car->getCarId()); ?>" class="btn btn-outline-accent">Purchase Options</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>
