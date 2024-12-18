<?php
require_once('db.php');  // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user inputs
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash password

    // Check if the email already exists in the database
    $check_email_sql = "SELECT email FROM users WHERE LOWER(email) = LOWER(?)";
    if ($stmt = $conn->prepare($check_email_sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        // If email already exists, show error message
        if ($stmt->num_rows > 0) {
            echo "Email already registered!";
            $stmt->close();  // Close the statement
            $conn->close();  // Close the connection
            exit;
        }
        $stmt->close();
    }

    // Prepare the SQL query to insert data into the database
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the query
        $stmt->bind_param("sss", $name, $email, $password);

        // Execute the query and handle success or failure
        if ($stmt->execute()) {
            // Redirect to the login page after successful signup
            header("Location: login.php");
            exit;
        } else {
            // If error occurs (e.g., email already exists), display appropriate message
            echo "Error executing query: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // Query preparation failed
        echo "Error preparing query: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>
