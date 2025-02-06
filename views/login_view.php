<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="../assets/images/tasks-solid.svg" type="image/x-icon" />

    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #2d3238;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border-radius: 8px;
        }
        .error-message {
            color: #ff6b6b;
            font-size: 14px;
            margin-top: 5px;
        }
        .form-control {
            background-color: #343a40;
            border-color: #454d55;
            color: #fff;
        }
        .form-control:focus {
            background-color: #3b4148;
            border-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container">
        <div class="login-container">
            <h1 class="text-center mb-4">Login</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                    <div class="error-message" id="username-error"></div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <div class="error-message" id="password-error"></div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="../register.php" class="text-primary">Register here</a></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            let isValid = true;
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            // Username validation
            if (username.length < 3) {
                document.getElementById('username-error').textContent = 'Username must be at least 3 characters';
                isValid = false;
            } else {
                document.getElementById('username-error').textContent = '';
            }

            // Password validation
            if (password.length < 6) {
                document.getElementById('password-error').textContent = 'Password must be at least 6 characters';
                isValid = false;
            } else {
                document.getElementById('password-error').textContent = '';
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
