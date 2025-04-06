document.addEventListener('DOMContentLoaded', function () {
  // Toggle display of the options menu for a car
  document.querySelectorAll('.options-btn').forEach(button => {
    button.addEventListener('click', function (event) {
      event.stopPropagation();
      const carId = this.getAttribute('data-car-id');
      const menu = document.getElementById('options-menu-' + carId);

      // Hide other menus first
      document.querySelectorAll('.options-menu').forEach(m => {
        if (m !== menu) m.style.display = 'none';
      });

      // Toggle visibility of the clicked menu
      menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
    });
  });

  // Hide menus when clicking outside
  document.addEventListener('click', function () {
    document.querySelectorAll('.options-menu').forEach(menu => {
      menu.style.display = 'none';
    });
  });

  // Handle edit and delete actions
  document.addEventListener('click', function (e) {
    // --- EDIT ---
    if (e.target.matches('.edit-car')) {
      e.preventDefault();
      const carId = e.target.getAttribute('data-car-id');

      fetch(`/api/cars/${carId}`)
        .then(res => res.json())
        .then(car => {
          const modalElement = document.getElementById('editCarModal');
          if (!modalElement) {
            console.error('Edit modal not found in DOM');
            alert('Edit modal is missing from the page.');
            return;
          }

          const modal = new bootstrap.Modal(modalElement);

          // Fill form inputs with fetched car data
          document.getElementById('editCarId').value = car.car_id;
          document.getElementById('editCarBrand').value = car.brand;
          document.getElementById('editCarModel').value = car.model;
          document.getElementById('editCarYear').value = car.year;
          document.getElementById('editCarTransmission').value = car.transmission;
          document.getElementById('editCarEngineSpec').value = car.engine_spec || '';
          document.getElementById('editCarCondition').value = car.car_condition || '';
          document.getElementById('editCarDescription').value = car.description || '';
          document.getElementById('editCarColor').value = car.color || '';
          document.getElementById('editCarPrice').value = car.price;
          document.getElementById('editCarOnSale').value = car.on_sale === 1 ? 'yes' : 'no';
          document.getElementById('editCarDiscount').value = car.discount;
          document.getElementById('editCarLeaseAvailable').value = car.lease_available === 1 ? 'yes' : 'no';
          document.getElementById('editCarLeaseTerms').value = car.lease_terms || '';
          document.getElementById('editCarStatus').value = car.status;
          document.getElementById('existingImagePath').value = car.image_path || '';

          modal.show();
        })
        .catch(err => {
          console.error('Failed to fetch car for editing:', err);
          alert('Could not load car details.');
        });

    // --- DELETE ---
    } else if (e.target.matches('.delete-car')) {
      e.preventDefault();
      const carId = e.target.getAttribute('data-car-id');
      if (confirm("Are you sure you want to remove this car?")) {
        fetch('/api/delete_car?car_id=' + encodeURIComponent(carId))
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const card = document.querySelector(`.card[data-car-id="${carId}"]`);
              if (card) card.remove();
            } else {
              alert("Failed to remove car: " + data.message);
            }
          })
          .catch(error => console.error("Error deleting car:", error));
      }
    }
  });

  // Handle submission of the edit form
  const editForm = document.getElementById('editCarForm');
  if (editForm) {
    editForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(editForm);

      fetch('/api/cars/edit', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Car updated successfully!');
            bootstrap.Modal.getInstance(document.getElementById('editCarModal')).hide();
            setTimeout(() => location.reload(), 300);
          } else {
            alert('Failed to update car: ' + data.message);
          }
        })
        .catch(err => {
          console.error('Error updating car:', err);
          alert('Something went wrong while updating.');
        });
    });
  }
});
