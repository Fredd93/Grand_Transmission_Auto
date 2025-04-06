<div class="modal fade" id="addCarModal" tabindex="-1" aria-labelledby="addCarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addCarForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCarModalLabel">Add New Car</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
          <!-- Basic Car Info -->
          <div class="mb-3">
            <label for="carBrand" class="form-label">Brand</label>
            <input type="text" class="form-control" id="carBrand" name="brand" required>
          </div>
          <div class="mb-3">
            <label for="carModel" class="form-label">Model</label>
            <input type="text" class="form-control" id="carModel" name="model" required>
          </div>
          <div class="mb-3">
            <label for="carYear" class="form-label">Year</label>
            <input type="number" class="form-control" id="carYear" name="year" step="1" required>
        </div>

          <div class="mb-3">
            <label for="carTransmission" class="form-label">Transmission</label>
            <select class="form-select" id="carTransmission" name="transmission" required>
              <option value="Automatic">Automatic</option>
              <option value="Manual">Manual</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="carEngineSpec" class="form-label">Engine Spec</label>
            <input type="text" class="form-control" id="carEngineSpec" name="engine_spec">
          </div>
          <div class="mb-3">
            <label for="carCondition" class="form-label">Car Condition</label>
            <input type="text" class="form-control" id="carCondition" name="car_condition" placeholder="e.g. New, Used, Certified">
          </div>
          <div class="mb-3">
            <label for="carDescription" class="form-label">Description</label>
            <textarea class="form-control" id="carDescription" name="description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="carColor" class="form-label">Color</label>
            <input type="text" class="form-control" id="carColor" name="color">
          </div>
          
          <!-- Pricing & Sale Info -->
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
          
          <!-- Lease Info -->
          <div class="mb-3">
            <label for="carLeaseAvailable" class="form-label">Lease Available?</label>
            <select class="form-select" id="carLeaseAvailable" name="lease_available">
              <option value="no">No</option>
              <option value="yes">Yes</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="carLeaseTerms" class="form-label">Lease Terms</label>
            <textarea class="form-control" id="carLeaseTerms" name="lease_terms" rows="2"></textarea>
          </div>
          
          <!-- Car Status -->
          <div class="mb-3">
            <label for="carStatus" class="form-label">Status</label>
            <select class="form-select" id="carStatus" name="status" required>
              <option value="available">Available</option>
              <option value="unavailable">Unavailable</option>
            </select>
          </div>
          
          <!-- Image Upload -->
          <div class="mb-3">
            <label for="carImage" class="form-label">Upload Image</label>
            <input type="file" class="form-control" id="image_path" name="image_path" accept="image/*" required>
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
