<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "suicide_rate_analysis";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// Fetch filters from the AJAX request
$year = isset($_GET['year']) ? $_GET['year'] : '';
$country = isset($_GET['country']) ? $_GET['country'] : '';
$age = isset($_GET['age']) ? $_GET['age'] : '';
$sex = isset($_GET['sex']) ? $_GET['sex'] : '';

// Build the WHERE clause dynamically based on filters
$whereClauses = [];
if ($year !== '') $whereClauses[] = "year = '$year'";
if ($country !== '') $whereClauses[] = "country = '$country'";
if ($age !== '') $whereClauses[] = "age = '$age'";
if ($sex !== '') $whereClauses[] = "sex = '$sex'";

$where = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Total Suicides
$totalSuicidesQuery = "SELECT SUM(suicide_no) AS total_suicides FROM suicide WHERE 1=1";
if ($year) $totalSuicidesQuery .= " AND year = '$year'";
if ($country) $totalSuicidesQuery .= " AND country = '$country'";
if ($age) $totalSuicidesQuery .= " AND age = '$age'";
if ($sex) $totalSuicidesQuery .= " AND sex = '$sex'";
$totalSuicidesResult = $conn->query($totalSuicidesQuery);
$totalSuicides = $totalSuicidesResult->fetch_assoc()['total_suicides'];

// Ensure total suicides is a whole number
$totalSuicides = is_null($totalSuicides) ? 0 : (int)$totalSuicides; // Convert to an integer


// Total Countries
$totalCountriesQuery = "SELECT COUNT(DISTINCT country) AS total_countries FROM suicide $where";
$totalCountriesResult = $conn->query($totalCountriesQuery);
$totalCountries = $totalCountriesResult->fetch_assoc()['total_countries'] ?? 0;

// Total Years
$totalYearsQuery = "SELECT COUNT(DISTINCT year) AS total_years FROM suicide $where";
$totalYearsResult = $conn->query($totalYearsQuery);
$totalYears = $totalYearsResult->fetch_assoc()['total_years'] ?? 0;

// Suicide Rate Over Time
$suicideTrendsQuery = "SELECT year, SUM(suicide_no) AS total_suicides FROM suicide $where GROUP BY year ORDER BY year";
$suicideTrendsResult = $conn->query($suicideTrendsQuery);
$suicideTrends = [];
while ($row = $suicideTrendsResult->fetch_assoc()) {
    $suicideTrends[$row['year']] = intval($row['total_suicides']);
}

// GDP vs Suicide Rate
$gdpVsSuicideQuery = "SELECT gdp_per_year, suicide_no FROM suicide WHERE 1=1";
if ($year) $gdpVsSuicideQuery .= " AND year = '$year'";
if ($country) $gdpVsSuicideQuery .= " AND country = '$country'";
if ($age) $gdpVsSuicideQuery .= " AND age = '$age'";
if ($sex) $gdpVsSuicideQuery .= " AND sex = '$sex'";
$gdpVsSuicideResult = $conn->query($gdpVsSuicideQuery);

$gdpVsSuicide = [];
while ($row = $gdpVsSuicideResult->fetch_assoc()) {
    $gdpVsSuicide[] = ['x' => $row['gdp_per_year'], 'y' => $row['suicide_no']];
}


// Top Countries by Suicide Rate
$topCountriesQuery = "SELECT country, AVG(suicide_100k_pop) AS avg_suicide_rate FROM suicide $where GROUP BY country ORDER BY avg_suicide_rate DESC LIMIT 10";
$topCountriesResult = $conn->query($topCountriesQuery);
$topCountries = [];
while ($row = $topCountriesResult->fetch_assoc()) {
    $topCountries[$row['country']] = floatval($row['avg_suicide_rate']);
}

// Suicide Rate by Generation
$generationQuery = "SELECT generation, SUM(suicide_no) AS total_suicides FROM suicide $where GROUP BY generation ORDER BY generation";
$generationResult = $conn->query($generationQuery);
$generationData = [];
while ($row = $generationResult->fetch_assoc()) {
    $generationData[$row['generation']] = intval($row['total_suicides']);
}

$gdpVsSuicideQuery = "SELECT gdp_per_year, AVG(suicide_100k_pop) AS avg_suicide_rate FROM suicide GROUP BY gdp_per_year";
$gdpVsSuicideResult = $conn->query($gdpVsSuicideQuery);

$gdpVsSuicide = [];
while ($row = $gdpVsSuicideResult->fetch_assoc()) {
    $gdpVsSuicide[] = [
        'x' => floatval($row['gdp_per_year']),
        'y' => floatval($row['avg_suicide_rate'])
    ];
}


// Return the data as JSON
echo json_encode([
    'totalSuicides' => $totalSuicides,
    'totalCountries' => $totalCountries,
    'totalYears' => $totalYears,
    'suicideTrends' => $suicideTrends,
    'gdpVsSuicide' => $gdpVsSuicide,
    'topCountries' => $topCountries,
    'generationData' => $generationData,
]);
$conn->close();
?>
