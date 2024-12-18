<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "suicide_rate_analysis";
$port = 3306;  // Specify the port if needed (default is 3306)

$conn = new mysqli($servername, $username, $password, $dbname, $port);  // Add port here

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // Show detailed error message
} else {
    echo "Connected successfully";  // This will confirm the connection
}
?>
