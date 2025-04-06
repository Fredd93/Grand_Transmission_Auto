document.addEventListener('DOMContentLoaded', function() {
    // Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
      console.error('Bootstrap is not loaded. Please include the Bootstrap JS bundle before addCar.js.');
      return;
    }
    
    // Get references to modal elements
    const addCarBtn = document.getElementById('addCarBtn');
    const addCarModalEl = document.getElementById('addCarModal');
    const addCarForm = document.getElementById('addCarForm');
    
    // If the addCarBtn exists, set up the modal functionality (only for employees)
    if (addCarBtn && addCarModalEl && addCarForm) {
      // Initialize Bootstrap modal (Bootstrap 5)
      const addCarModal = new bootstrap.Modal(addCarModalEl);
      
      // When the "Add Car" button is clicked, reset the form and show the modal
      addCarBtn.addEventListener('click', function() {
        addCarForm.reset();
        addCarModal.show();
      });
      
      // Handle form submission
      addCarForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Gather form data
        const formData = new FormData(addCarForm);
        
        // Send POST request to the add_car endpoint
        fetch('/api/add_car.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Car added successfully!');
            addCarModal.hide();
            // Optionally, refresh the page or update the car grid via AJAX
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error("Error adding car:", error);
          alert("An error occurred while adding the car.");
        });
      });
    }
  });
  