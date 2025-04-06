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

  // Event delegation for Edit and Delete
  document.addEventListener('click', function (e) {
    if (e.target.matches('.edit-car')) {
      e.preventDefault();
      const carId = e.target.getAttribute('data-car-id');
      console.log("Edit car", carId);
      // TODO: open edit modal
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
});
