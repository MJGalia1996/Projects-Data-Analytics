<?php
session_start();

// If user is already logged out, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Logout logic will be handled by JavaScript confirmation
if (isset($_POST['confirm_logout'])) {
    session_destroy(); // Destroy all session data
    header("Location: login.php"); // Redirect to the login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            background: linear-gradient(to right, #ff758c, #ff7eb3);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #ff4d4d;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #ff1a1a;
        }
    </style>
</head>
<body>
    <!-- Logout Button in the Upper Right Corner -->
    <button class="logout-btn" onclick="confirmLogout()">Logout</button>

    <script>
        // Function to show a confirmation popup for logout
        function confirmLogout() {
            var confirmation = confirm("Are you sure you want to log out?");
            if (confirmation) {
                window.location.href = "logout.php?confirm=true"; // Redirect to the logout script to destroy session
            }
        }
    </script>
</body>
</html>

