document.addEventListener('DOMContentLoaded', function () {
  if (typeof bootstrap === 'undefined') {
    console.error('Bootstrap is not loaded. Please include it before addCar.js.');
    return;
  }

  const addCarBtn = document.getElementById('addCarBtn');
  const addCarModalEl = document.getElementById('addCarModal');
  const addCarForm = document.getElementById('addCarForm');

  if (addCarBtn && addCarModalEl && addCarForm) {
    const addCarModal = new bootstrap.Modal(addCarModalEl);

    // Show modal and reset form
    addCarBtn.addEventListener('click', function () {
      addCarForm.reset();
      addCarModal.show();
    });

    // Clean up modal backdrop on close
    addCarModalEl.addEventListener('hidden.bs.modal', () => {
      addCarForm.reset();
      removeModalBackdrop();
    });

    addCarForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(addCarForm);
      const imageInput = document.getElementById('image_path');

      if (!imageInput.files || imageInput.files.length === 0) {
        alert("Please select an image to upload.");
        return;
      }

      fetch('/api/add_car.php', {
        method: 'POST',
        body: formData
      })
        .then(async response => {
          const contentType = response.headers.get("content-type");

          if (contentType && contentType.includes("application/json")) {
            const data = await response.json();
            if (data.success) {
              alert('Car added successfully!');
              addCarModal.hide();
              setTimeout(() => {
                removeModalBackdrop();
                location.reload();
              }, 200);
            } else {
              alert('Error: ' + data.message);
            }
          } else {
            const errorText = await response.text();
            console.error("Unexpected response (not JSON):", errorText);
            alert("An unexpected server error occurred. Check the console for details.");
          }
        })
        .catch(error => {
          console.error("Error adding car:", error);
          alert("An error occurred while adding the car.");
        });
    });
  }

  function removeModalBackdrop() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
  }
});
