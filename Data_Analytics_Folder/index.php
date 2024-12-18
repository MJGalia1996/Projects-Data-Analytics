<?php
session_start();


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "suicide_rate_analysis"; // Replace with your actual database name

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get distinct years for dropdown
$yearsQuery = "SELECT DISTINCT year FROM suicide ORDER BY year ASC";
$yearsResult = $conn->query($yearsQuery);

// Get distinct countries for dropdown
$countriesQuery = "SELECT DISTINCT country FROM suicide ORDER BY country ASC";
$countriesResult = $conn->query($countriesQuery);

// Get distinct age groups for dropdown
$ageGroupsQuery = "SELECT DISTINCT age FROM suicide ORDER BY age ASC";
$ageGroupsResult = $conn->query($ageGroupsQuery);

// Get distinct sex for dropdown
$sexQuery = "SELECT DISTINCT sex FROM suicide ORDER BY sex ASC";
$sexResult = $conn->query($sexQuery);

// Get distinct generations for dropdown
$generationsQuery = "SELECT DISTINCT generation FROM suicide ORDER BY generation ASC";
$generationsResult = $conn->query($generationsQuery);

// Total Suicides Query
$totalSuicidesQuery = "SELECT SUM(suicide_no) AS total_suicides FROM suicide";
$totalSuicidesResult = $conn->query($totalSuicidesQuery);
$totalSuicides = 0;
if ($totalSuicidesResult->num_rows > 0) {
    $row = $totalSuicidesResult->fetch_assoc();
    $totalSuicides = round($row['total_suicides']);
}

// Fetch data for the charts (Suicide Rate Over Time, GDP vs Suicide Rate, Top Countries by Suicide Rate, and Generation Data)
$suicideTrendsQuery = "SELECT year, SUM(suicide_no) AS total_suicides FROM suicide GROUP BY year ORDER BY year";
$suicideTrendsResult = $conn->query($suicideTrendsQuery);

$gdpVsSuicideQuery = "SELECT gdp_per_year, AVG(suicide_100k_pop) AS avg_suicide_rate FROM suicide GROUP BY gdp_per_year";
$gdpVsSuicideResult = $conn->query($gdpVsSuicideQuery);

$topCountriesQuery = "SELECT country, AVG(suicide_100k_pop) AS avg_suicide_rate FROM suicide GROUP BY country ORDER BY avg_suicide_rate DESC LIMIT 10";
$topCountriesResult = $conn->query($topCountriesQuery);

$generationQuery = "SELECT generation, year, SUM(suicide_no) AS total_suicides FROM suicide GROUP BY generation, year ORDER BY year";
$generationResult = $conn->query($generationQuery);

// Prepare data for charts
$suicideTrends = [];
while ($row = $suicideTrendsResult->fetch_assoc()) {
    $suicideTrends[$row['year']] = $row['total_suicides'];
}

$gdpVsSuicide = [];
while ($row = $gdpVsSuicideResult->fetch_assoc()) {
    $gdpVsSuicide[] = ['x' => intval($row['gdp_per_year']), 'y' => floatval($row['avg_suicide_rate'])];

}

$topCountries = [];
while ($row = $topCountriesResult->fetch_assoc()) {
    $topCountries[$row['country']] = $row['avg_suicide_rate'];
}

$generationData = [];
$generationTrends = [];
while ($row = $generationResult->fetch_assoc()) {
    $generationData[$row['generation']] = $row['total_suicides'];
    if (!isset($generationTrends[$row['generation']])) {
        $generationTrends[$row['generation']] = [];
    }
    $generationTrends[$row['generation']][$row['year']] = $row['total_suicides'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suicide Rate Analysis Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            padding: 30px 0;
        }

        h1 {
            text-align: center;
            font-size: 48px;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            margin-bottom: 40px;
        }

        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #FF6F61;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background-color: #FF3B2D;
        }

        .filter-label {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }

        .filter-select {
            display: block;
            width: 100%;
            padding: 12px;
            border: 1px solid #666;
            background-color: #444;
            color: #fff;
            border-radius: 8px;
            font-size: 18px;
        }

        .card-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
        }

        .card {
            background-color: #333;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #fff;
            flex: 1;
            max-width: 300px;
            margin: 10px;
        }

        .card i {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .card h5 {
            font-size: 18px;
            font-weight: 600;
        }

        .card p {
            font-size: 28px;
            font-weight: bold;
        }

        .chart-container {
            background-color: #808080;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #fff;
        }

        .chart-container h5 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 15px;
        }

        canvas {
            background-color: #ffffff;
            border-radius: 4px;
        }

        .chart-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 30px;
        }

        .chart-container {
            flex: 1;
            min-width: 22%;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
    <a href="javascript:void(0);" onclick="confirmLogout()">
        <button class="logout-btn">Logout</button>
    </a>
<?php else: ?>
    <a href="signup.php">
        <button class="signup-btn">Sign Up</button>
    </a>
<?php endif; ?>

<h1>SUICIDE RATE ANALYSIS DASHBOARD</h1>

<!-- Filters -->
<div style="display: flex; justify-content: center; gap: 20px; margin: 30px 0;">
    <div>
        <label class="filter-label">Filter by Year</label>
        <select id="yearFilter" class="filter-select">
            <option value="">Select Year</option>
            <?php while ($row = $yearsResult->fetch_assoc()): ?>
                <option value="<?php echo $row['year']; ?>"><?php echo $row['year']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div>
        <label class="filter-label">Filter by Country</label>
        <select id="countryFilter" class="filter-select">
            <option value="">Select Country</option>
            <?php while ($row = $countriesResult->fetch_assoc()): ?>
                <option value="<?php echo $row['country']; ?>"><?php echo $row['country']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div>
        <label class="filter-label">Filter by Age Group</label>
        <select id="ageFilter" class="filter-select">
            <option value="">Select Age Group</option>
            <?php while ($row = $ageGroupsResult->fetch_assoc()): ?>
                <option value="<?php echo $row['age']; ?>"><?php echo $row['age']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div>
        <label class="filter-label">Filter by Sex</label>
        <select id="sexFilter" class="filter-select">
            <option value="">Select Sex</option>
            <?php while ($row = $sexResult->fetch_assoc()): ?>
                <option value="<?php echo $row['sex']; ?>"><?php echo $row['sex']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
</div>

 <!-- Overview Cards -->
 <div class="card-container">
    <div class="card">
        <i class="bi bi-emoji-heart-eyes" style="color: #FF6F61;"></i>
        <h5>Total Suicides</h5>
        <p id="totalSuicides"><?php echo round ($totalSuicides); ?></p>
    </div>
    <div class="card">
        <i class="bi bi-globe2" style="color: #61D3FF;"></i>
        <h5>Total Countries</h5>
        <p id="totalCountries"><?php echo count($topCountries); ?></p>
    </div>
    <div class="card">
        <i class="bi bi-calendar-check" style="color: #4CAF50;"></i>
        <h5>Available Years</h5>
        <p id="totalYears"><?php echo count($suicideTrends); ?></p>
    </div>
</div>

    <!-- Chart Containers -->
    <div class="chart-row">
    <div class="chart-container">
        <h5>Suicide Rate Over Time</h5>
        <canvas id="suicideTrendsChart"></canvas>
    </div>
    <div class="chart-container">
        <h5>GDP vs Suicide Rate</h5>
        <canvas id="gdpVsSuicideChart" width="400" height="200"></canvas>
    </div>
    <div class="chart-container">
        <h5>Top Countries by Suicide Rate</h5>
        <canvas id="topCountriesChart"></canvas>
    </div>
    <div class="chart-container">
        <h5>Suicide Rate by Generation</h5>
        <canvas id="generationChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    // Function to update the dashboard data
    function updateDashboard() {
        var year = $('#yearFilter').val();
        var country = $('#countryFilter').val();
        var age = $('#ageFilter').val();
        var sex = $('#sexFilter').val();

        $.ajax({
            url: 'get_dashboard_data.php', // PHP script that queries the database
            method: 'GET',
            data: { year: year, country: country, age: age, sex: sex },
            success: function(response) {
                var data = JSON.parse(response);

                // Update cards
                $('#totalSuicides').text(data.totalSuicides);
                $('#totalCountries').text(data.totalCountries);
                $('#totalYears').text(data.totalYears);

                // Update charts
                updateCharts(data.suicideTrends, data.gdpVsSuicide, data.topCountries, data.generationData);
            }
        });
    }

    // Function to update charts
    function updateCharts(suicideTrends, gdpVsSuicide, topCountries, generationData) {
        const years = Object.keys(suicideTrends);
        const suicideRates = Object.values(suicideTrends);
        const gdpValues = gdpVsSuicide.map(obj => obj.x);
        const suicideValues = gdpVsSuicide.map(obj => obj.y);
        const countryNames = Object.keys(topCountries);
        const countryRates = Object.values(topCountries);
        const generationLabels = Object.keys(generationData);
        const generationCounts = Object.values(generationData);
        // Suicide Trends Chart
        createChart('suicideTrendsChart', 'line', years, [{
            label: 'Suicide Rate',
            data: suicideRates,
            borderColor: 'rgba(255, 99, 132, 1)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: true
        }]);

        // GDP vs Suicide Rate Chart
        createChart('gdpVsSuicideChart', 'scatter', gdpValues, [{
            label: 'GDP vs Suicide Rate',
            data: gdpVsSuicide,
            backgroundColor: 'rgba(75, 192, 192, 1)'
        }]);

        // Top Countries by Suicide Rate Chart
        createChart('topCountriesChart', 'bar', countryNames, [{
            label: 'Suicide Rate by Country',
            data: countryRates,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]);

        // Generation Chart (Pie Chart)
        createChart('generationChart', 'line', generationLabels, [{
            label: 'Generation Suicide Rate Distribution',
            data: generationCounts,
            backgroundColor: ['#FF6F61', '#61D3FF', '#4CAF50', '#FFEB3B', '#9C27B0'],
        }]);
    }

    // Function to create a chart
function createChart(ctxId, type, labels, datasets) {
    new Chart(document.getElementById(ctxId).getContext('2d'), {
        type: type,
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#000',
                        boxWidth: 15
                    },
                    backgroundColor: 'white'
                },
            }
        }
    });
}

function confirmLogout() {
    if (confirm('Are you sure you want to log out?')) {
        window.location.href = 'login.php'; // Redirect to login page after logout confirmation
    }
}
    // Call updateDashboard when filters are changed
    $('#yearFilter, #countryFilter, #ageFilter, #sexFilter').on('change', function() {
        updateDashboard();
    });

    // Initial call to load data
    updateDashboard();
</script>
</body>
</html>