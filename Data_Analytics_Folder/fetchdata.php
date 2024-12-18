<?php
header('Content-Type: application/json');
include 'db.php'; // Include the database connection

// Queries to fetch distinct dropdown values
$yearsQuery = "SELECT DISTINCT year FROM suicide ORDER BY year ASC";
$countriesQuery = "SELECT DISTINCT country FROM suicide ORDER BY country ASC";
$agesQuery = "SELECT DISTINCT age FROM suicide ORDER BY age ASC";
$sexesQuery = "SELECT DISTINCT sex FROM suicide ORDER BY sex ASC";

// Initialize response array
$response = [
    "years" => [],
    "countries" => [],
    "ages" => [],
    "sexes" => []
];

// Fetch years
$yearsResult = $conn->query($yearsQuery);
if ($yearsResult) {
    while ($row = $yearsResult->fetch_assoc()) {
        $response['years'][] = $row['year'];
    }
}

// Fetch countries
$countriesResult = $conn->query($countriesQuery);
if ($countriesResult) {
    while ($row = $countriesResult->fetch_assoc()) {
        $response['countries'][] = $row['country'];
    }
}

// Fetch ages
$agesResult = $conn->query($agesQuery);
if ($agesResult) {
    while ($row = $agesResult->fetch_assoc()) {
        $response['ages'][] = $row['age'];
    }
}

// Fetch sexes
$sexesResult = $conn->query($sexesQuery);
if ($sexesResult) {
    while ($row = $sexesResult->fetch_assoc()) {
        $response['sexes'][] = $row['sex'];
    }
}

// Return response as JSON
echo json_encode($response);
?>
