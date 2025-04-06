document.addEventListener('DOMContentLoaded', function() {
    // Get references to our filter elements
    const brandSelect = document.getElementById('brandSelect');
    const yearSelect = document.getElementById('yearSelect');
    const transmissionSelect = document.getElementById('transmissionSelect');
    const onSaleSelect = document.getElementById('onSaleSelect');
    const priceMinInput = document.getElementById('priceMin');
    const priceMaxInput = document.getElementById('priceMax');
    const filterBtn = document.getElementById('filterBtn');
    const resultsContainer = document.getElementById('results');
  
    // 1. Fetch distinct filter values from the API endpoint
    fetch('/api/car_filter.php')
      .then(response => response.json())
      .then(data => {
        // Populate brand dropdown
        data.brands.forEach(brand => {
          const opt = document.createElement('option');
          opt.value = brand;
          opt.textContent = brand;
          brandSelect.appendChild(opt);
        });
        // Populate year dropdown
        data.years.forEach(year => {
          const opt = document.createElement('option');
          opt.value = year;
          opt.textContent = year;
          yearSelect.appendChild(opt);
        });
        // Populate transmission dropdown
        data.transmissions.forEach(transmission => {
          const opt = document.createElement('option');
          opt.value = transmission;
          opt.textContent = transmission;
          transmissionSelect.appendChild(opt);
        });
        // Populate on_sale dropdown
        data.on_sale_values.forEach(val => {
          const opt = document.createElement('option');
          opt.value = val;
          opt.textContent = val;
          onSaleSelect.appendChild(opt);
        });
        // Optionally, set the placeholders for price inputs based on the database bounds
        if (data.price_bounds) {
          priceMinInput.placeholder = data.price_bounds.min_price;
          priceMaxInput.placeholder = data.price_bounds.max_price;
        }
      })
      .catch(error => console.error("Error fetching filter data:", error));
  
    // 2. When the filter button is clicked, build query parameters and fetch filtered cars
    filterBtn.addEventListener('click', function() {
      const params = new URLSearchParams();
  
      if (brandSelect.value) params.append('brand', brandSelect.value);
      if (yearSelect.value) params.append('year', yearSelect.value);
      if (transmissionSelect.value) params.append('transmission', transmissionSelect.value);
      if (onSaleSelect.value) params.append('on_sale', onSaleSelect.value);
      if (priceMinInput.value) params.append('price_min', priceMinInput.value);
      if (priceMaxInput.value) params.append('price_max', priceMaxInput.value);
  
      // Fetch filtered cars from your API endpoint
      fetch('/api/get_cars?' + params.toString())
        .then(response => response.json())
        .then(cars => {
          // Clear previous results
          resultsContainer.innerHTML = '';
  
          if (cars.length === 0) {
            resultsContainer.innerHTML = '<p>No cars match the selected filters.</p>';
            return;
          }
  
          // Render each car as a card
          cars.forEach(car => {
            const cardDiv = document.createElement('div');
            cardDiv.classList.add('card');
  
            // Convert price and discount to numbers
            const price = Number(car.price);
            const discount = Number(car.discount);
            let priceHTML = `<p class="card-text">Price: $${price.toFixed(2)}</p>`;
  
            // If the car is on sale and discount is greater than 0, calculate new price
            if (car.on_sale === 'yes' && discount > 0) {
              const newPrice = price * (1 - discount / 100);
              priceHTML = `<p class="card-text">Price: <del>$${price.toFixed(2)}</del> <span class="text-success">$${newPrice.toFixed(2)}</span></p>`;
            }
  
            cardDiv.innerHTML = `
              <img src="/assets/images/${car.image_path}" class="card-img-top" alt="${car.brand} ${car.model}">
              <div class="card-body text-center">
                <h5 class="card-title">${car.brand} ${car.model}</h5>
                ${priceHTML}
                ${car.on_sale === 'yes' && discount > 0 ? `<p class="text-danger">On Sale: ${discount}% off</p>` : ''}
                <a href="/details.php?id=${encodeURIComponent(car.car_id)}" class="btn btn-outline-accent">View Details</a>
                <a href="/purchase.php?id=${encodeURIComponent(car.car_id)}" class="btn btn-outline-accent">Purchase Options</a>
              </div>
            `;
            resultsContainer.appendChild(cardDiv);
          });
        })
        .catch(error => console.error("Error fetching filtered cars:", error));
    });
  });
  