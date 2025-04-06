// public/assets/js/createOrder.js
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("createOrderForm");
  
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
  
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());
  
      try {
        const response = await fetch("/api/orders/create", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        });
  
        const result = await response.json();
  
        if (response.ok && result.success) {
          alert("Order created successfully!");
          window.location.href = "/order"; // redirect to orders page
        } else {
          alert("Error: " + result.message);
        }
      } catch (err) {
        console.error("Error submitting order:", err);
        alert("Something went wrong while submitting the order.");
      }
    });
  });
  