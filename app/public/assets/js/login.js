document.addEventListener("DOMContentLoaded", function() {
    // Check if the login form exists on this page
    var loginForm = document.getElementById("login-form");
    if (!loginForm) {
        console.warn("Login form not found on this page.");
        return;
    }
    
    // Attach the submit event listener
    loginForm.addEventListener("submit", function(e) {
        e.preventDefault(); // Prevent default form submission

        // Create FormData from the form
        var formData = new FormData(loginForm);
        console.log("Submitting login form with data:", [...formData.entries()]);
      
        // Make the fetch request to the API endpoint
        fetch("/api/login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // If login is successful, redirect the user
                window.location.href = data.redirect;
            } else {
                // Display error message if login fails
                var errorDiv = document.getElementById("error-message");
                if (errorDiv) {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = "block";
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error("Error during login:", error);
        });
    });
});
