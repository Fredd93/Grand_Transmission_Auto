document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('carContent');
  
    fetch(`/api/cars/${carId}`)
      .then(res => res.json())
      .then(car => {
        if (car.error) {
          container.innerHTML = `<p class="text-danger">${car.error}</p>`;
          return;
        }
  
        container.innerHTML = `
          <div class="row">
            <div class="col-md-5 text-center">
              <img src="/${car.image_path}" class="img-fluid rounded" alt="${car.brand} ${car.model}">
            </div>
            <div class="col-md-7">
              <h3>${car.brand} ${car.model} (${car.year})</h3>
              <p><strong>Transmission:</strong> ${car.transmission}</p>
              <p><strong>Engine Spec:</strong> ${car.engine_spec || '—'}</p>
              <p><strong>Condition:</strong> ${car.car_condition || '—'}</p>
              <p><strong>Color:</strong> ${car.color || '—'}</p>
              <p><strong>Price:</strong> $${parseFloat(car.price).toFixed(2)}</p>
              <p><strong>On Sale:</strong> ${car.on_sale}</p>
              <p><strong>Discount:</strong> ${car.discount}%</p>
              <p><strong>Lease Available:</strong> ${car.lease_available}</p>
              <p><strong>Lease Terms:</strong> ${car.lease_terms || '—'}</p>
              <p><strong>Status:</strong> ${car.status}</p>
              <p><strong>Description:</strong><br>${car.description || 'No description available.'}</p>
            </div>
          </div>
        `;
      })
      .catch(err => {
        console.error("Error fetching car details:", err);
        container.innerHTML = `<p class="text-danger">Failed to load car details.</p>`;
      });
  });
  