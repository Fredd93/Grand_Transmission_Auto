<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GT Autos</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Include Bootstrap CSS if needed -->
</head>
<body>
    <div class="container login-container">
    <h2>Login</h2>
    <div id="error-message" class="alert alert-danger" style="display: none;"></div>
    <form id="login-form">
        <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
        </div>
        <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
        <div style="margin-top: 1rem; text-align: center;">
            <button type="button" class="btn btn-outline-accent" onclick="window.location.href='/'">
            oops I'm not an employee take me back
            </button>
        </div>
    </div>


    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);

            fetch('/api/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    document.getElementById('error-message').textContent = data.message;
                    document.getElementById('error-message').style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
    <script src="../../assets/js/login.js"></script>
</body>
</html>
