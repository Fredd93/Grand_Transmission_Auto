document.addEventListener('DOMContentLoaded', function () {
  const brandSelect = document.getElementById('brandSelect');
  const yearSelect = document.getElementById('yearSelect');
  const transmissionSelect = document.getElementById('transmissionSelect');
  const onSaleSelect = document.getElementById('onSaleSelect');
  const priceMinInput = document.getElementById('priceMin');
  const priceMaxInput = document.getElementById('priceMax');
  const filterBtn = document.getElementById('filterBtn');
  const resultsContainer = document.getElementById('results');

  if (!brandSelect || !yearSelect || !transmissionSelect || !onSaleSelect || !priceMinInput || !priceMaxInput || !filterBtn || !resultsContainer) {
    console.error("One or more filter elements not found on the page.");
    return;
  }

  // Fetch filter options
  fetch('/api/cars/filters')
    .then(res => res.json())
    .then(data => {
      populateSelect(brandSelect, data.brands);
      populateSelect(yearSelect, data.years);
      populateSelect(transmissionSelect, data.transmissions);
      populateSelect(onSaleSelect, data.on_sale_values);

      if (data.price_bounds) {
        priceMinInput.placeholder = data.price_bounds.min_price;
        priceMaxInput.placeholder = data.price_bounds.max_price;
      }

      // Load all cars initially
      fetch('/api/cars')
        .then(res => res.json())
        .then(renderCars)
        .catch(err => console.error("Error loading initial cars:", err));
    })
    .catch(err => console.error("Error loading filters:", err));

  // Filtering logic
  filterBtn.addEventListener('click', function () {
    const params = new URLSearchParams();

    if (brandSelect.value) params.append('brand', brandSelect.value);
    if (yearSelect.value) params.append('year', yearSelect.value);
    if (transmissionSelect.value) params.append('transmission', transmissionSelect.value);
    if (onSaleSelect.value) params.append('on_sale', onSaleSelect.value);
    if (priceMinInput.value) params.append('price_min', priceMinInput.value);
    if (priceMaxInput.value) params.append('price_max', priceMaxInput.value);

    fetch('/api/cars/filter', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(Object.fromEntries(params))
    })
      .then(res => res.json())
      .then(renderCars)
      .catch(err => console.error("Error fetching filtered cars:", err));
  });

  function populateSelect(selectElement, options) {
    options.forEach(value => {
      const opt = document.createElement('option');
      opt.value = value;
      opt.textContent = value;
      selectElement.appendChild(opt);
    });
  }

  function renderCars(cars) {
    resultsContainer.innerHTML = '';

    if (!cars || cars.length === 0) {
      resultsContainer.innerHTML = '<p>No cars match your selection.</p>';
      return;
    }

    cars.forEach(car => {
      const card = document.createElement('div');
      card.className = 'card';

      const price = Number(car.price);
      const discount = Number(car.discount);
      const isOnSale = car.on_sale === 'yes' && discount > 0;

      const priceHTML = isOnSale
        ? `<p class="card-text">Price: <del>$${price.toFixed(2)}</del> <span class="text-success">$${(price * (1 - discount / 100)).toFixed(2)}</span></p>`
        : `<p class="card-text">Price: $${price.toFixed(2)}</p>`;

      card.innerHTML = `
        <img src="/assets/images/${car.image_path}" class="card-img-top" alt="${car.brand} ${car.model}">
        <div class="card-body text-center">
          <h5 class="card-title">${car.brand} ${car.model}</h5>
          ${priceHTML}
          ${isOnSale ? `<p class="text-danger">On Sale: ${discount}% off</p>` : ''}
          <a href="/details.php?id=${encodeURIComponent(car.car_id)}" class="btn btn-outline-accent">View Details</a>
          <a href="/purchase.php?id=${encodeURIComponent(car.car_id)}" class="btn btn-outline-accent">Purchase Options</a>
        </div>
      `;

      resultsContainer.appendChild(card);
    });
  }
});
