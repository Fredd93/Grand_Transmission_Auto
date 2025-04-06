document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById("login-form");
    if (!loginForm) {
        console.warn("Login form not found on this page.");
        return;
    }

    loginForm.addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(loginForm);
        console.log("Submitting login form with data:", [...formData.entries()]);

        fetch("/api/login", { // ✅ NEW route (handled by AuthApiController)
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                const errorDiv = document.getElementById("error-message");
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
