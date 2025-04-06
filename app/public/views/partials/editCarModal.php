<div class="mb-3">
  <label for="editCarYear" class="form-label">Year</label>
  <input type="number" class="form-control" id="editCarYear" name="year" required>
</div>

<div class="mb-3">
  <label for="editCarTransmission" class="form-label">Transmission</label>
  <select class="form-select" id="editCarTransmission" name="transmission" required>
    <option value="Automatic">Automatic</option>
    <option value="Manual">Manual</option>
  </select>
</div>

<div class="mb-3">
  <label for="editCarEngineSpec" class="form-label">Engine Spec</label>
  <input type="text" class="form-control" id="editCarEngineSpec" name="engine_spec">
</div>

<div class="mb-3">
  <label for="editCarCondition" class="form-label">Car Condition</label>
  <input type="text" class="form-control" id="editCarCondition" name="car_condition">
</div>

<div class="mb-3">
  <label for="editCarDescription" class="form-label">Description</label>
  <textarea class="form-control" id="editCarDescription" name="description"></textarea>
</div>

<div class="mb-3">
  <label for="editCarColor" class="form-label">Color</label>
  <input type="text" class="form-control" id="editCarColor" name="color">
</div>

<div class="mb-3">
  <label for="editCarPrice" class="form-label">Price</label>
  <input type="number" step="0.01" class="form-control" id="editCarPrice" name="price" required>
</div>

<div class="mb-3">
  <label for="editCarOnSale" class="form-label">On Sale?</label>
  <select class="form-select" id="editCarOnSale" name="on_sale" required>
    <option value="no">No</option>
    <option value="yes">Yes</option>
  </select>
</div>

<div class="mb-3">
  <label for="editCarDiscount" class="form-label">Discount (%)</label>
  <input type="number" step="0.01" class="form-control" id="editCarDiscount" name="discount" value="0.00" required>
</div>

<div class="mb-3">
  <label for="editCarLeaseAvailable" class="form-label">Lease Available?</label>
  <select class="form-select" id="editCarLeaseAvailable" name="lease_available">
    <option value="no">No</option>
    <option value="yes">Yes</option>
  </select>
</div>

<div class="mb-3">
  <label for="editCarLeaseTerms" class="form-label">Lease Terms</label>
  <textarea class="form-control" id="editCarLeaseTerms" name="lease_terms"></textarea>
</div>

<div class="mb-3">
  <label for="editCarStatus" class="form-label">Status</label>
  <select class="form-select" id="editCarStatus" name="status" required>
    <option value="available">Available</option>
    <option value="unavailable">Unavailable</option>
  </select>
</div>
