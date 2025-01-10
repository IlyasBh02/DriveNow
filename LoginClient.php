<?php
session_start();  // Assurez-vous que session_start() est le tout premier appel PHP

// Inclure les classes nécessaires
require_once 'Database.php';
require_once 'Auth.php'; 

$error = '';

if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $error = "Please provide both email and password.";
    } else {
        $database = new Database();
        $auth = new Auth($database);

        $result = $auth->login($email, $password);

        if ($result == 'admin') {
            header("Location:dashboardAdmin.php");
            exit();
        } elseif ($result == 'client') {
            header("Location: index.php");
            exit();
        } else {
            $error = $result;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <main class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Login</h2>
                <p class="mt-2 text-sm text-gray-600">
                    New here? 
                    <a href="registerClient.php" class="font-medium text-indigo-600 hover:text-indigo-500">Sign up here</a>
                </p>
            </div>

            <form action="" method="POST" class="mt-8 space-y-6" id="loginForm">
                <div class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label for="email" class="flex items-center text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="input-field" required>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="flex items-center text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="input-field" required>
                    </div>
                </div>

                <div>
                    <button type="submit" name="submit" class="submit-btn">Login</button>
                    <?php if ($error): ?>
                        <p class="text-red-500 mt-2"><?php echo $error; ?></p>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.getElementById("loginForm").onsubmit = function() {
            let isValid = true;
            const email = document.getElementById("email");
            const password = document.getElementById("password");
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!email.value.trim() || !emailRegex.test(email.value.trim())) {
                isValid = false;
                alert("Please enter a valid email.");
            }

            if (!password.value.trim()) {
                isValid = false;
                alert("Please enter a password.");
            }

            return isValid;
        };
    </script>
</body>
</html>
