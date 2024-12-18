<?php
session_start();  // Start session to manage user login

// Include your database connection
require_once('db.php');  // Ensure you have db.php where your MySQL connection is handled

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL to get the user details
    $sql = "SELECT id, password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        // Bind the email parameter
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Password is correct, start the session
                $_SESSION['user_id'] = $user_id;
                // Redirect to dashboard page (change the link as needed)
                header("Location: suicide_rate_dashboard.php");
                exit();
            } else {
                // Incorrect password
                echo "Invalid password. Please try again.";
            }
        } else {
            // No user found with this email
            echo "No account found with that email address.";
        }

        $stmt->close();  // Close statement
    } else {
        echo "Error preparing query: " . $conn->error;
    }

    $conn->close();  // Close the database connection
}
?>
