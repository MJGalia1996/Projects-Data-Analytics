<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "suicide_rate_analysis";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Verify password (if hashed)
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            // Redirect to index.php
            header("Location: index.php");
            exit();
        } else {
            echo "<p style='color: red; text-align: center;'>Incorrect password.</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>No account found with that email address.</p>";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .container h1 {
            text-align: center;
            font-size: 2rem;
            color: #333333;
        }
        .container p {
            text-align: center;
            color: #666666;
        }
        .container form {
            margin-top: 20px;
        }
        .container form div {
            margin-bottom: 15px;
        }
        .container form input {
            width: 90%;
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }
        .container form button {
            width: 50%;
            padding: 8px 10px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #ffffff;
            cursor: pointer;
            display: block;
            margin: 10px auto;
            transition: background-color 0.3s; 
        }
        .container a {
            color: #007bff;
            text-decoration: none;
            text-align: center;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: center; margin-bottom: 20px;">
            <i class="fas fa-user-circle" style="font-size: 3rem; color: #007bff;"></i>
        </div>
        <h1>Welcome Back!</h1>
        <p>Log in to your account and continue your journey with us.</p>
        <form method="POST" action="login.php">
            <div>
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <div>
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div>
                <button type="submit">Login <i class="fas fa-sign-in-alt"></i></button>
            </div>
        </form>
        <a href="signup.php">Don't have an account? Sign up here</a>
    </div>
</body>
</html>
