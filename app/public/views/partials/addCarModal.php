<?php
// ... your page content (header, filter panel, car grid, etc.)
?>

<!-- Add Car Modal (Bootstrap 5 style) -->
<div class="modal fade" id="addCarModal" tabindex="-1" aria-labelledby="addCarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addCarForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCarModalLabel">Add New Car</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Form fields for adding a car -->
          <div class="mb-3">
            <label for="carBrand" class="form-label">Brand</label>
            <input type="text" class="form-control" id="carBrand" name="brand" required>
          </div>
          <div class="mb-3">
            <label for="carModel" class="form-label">Model</label>
            <input type="text" class="form-control" id="carModel" name="model" required>
          </div>
          <div class="mb-3">
            <label for="carPrice" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="carPrice" name="price" required>
          </div>
          <div class="mb-3">
            <label for="carOnSale" class="form-label">On Sale?</label>
            <select class="form-select" id="carOnSale" name="on_sale" required>
              <option value="no">No</option>
              <option value="yes">Yes</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="carDiscount" class="form-label">Discount (%)</label>
            <input type="number" step="0.01" class="form-control" id="carDiscount" name="discount" value="0.00" required>
          </div>
          <div class="mb-3">
            <label for="carImagePath" class="form-label">Image Path</label>
            <input type="text" class="form-control" id="carImagePath" name="image_path" placeholder="../../assets/images/your_image.jpg" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Car</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php
// Then include the footer partial (which closes the <body> and </html> tags)
include __DIR__ . '/../partials/footer.php';
?>

<!-- Include your dedicated JS files -->
<script src="/assets/js/carFilter.js"></script>
<script src="/assets/js/addCar.js"></script>
