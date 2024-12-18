// Fetch dropdown data and populate dropdowns
async function fetchDropdownData() {
    try {
        const response = await fetch('fetchdata.php'); // Adjust path if needed
        const data = await response.json();

        if (data.error) {
            console.error('Error from server:', data.error);
            return;
        }

        // Populate Year Filter
        const yearDropdown = document.getElementById('yearFilter');
        yearDropdown.innerHTML = '<option value="">Select Year</option>';
        data.years.forEach(year => {
            yearDropdown.innerHTML += `<option value="${year}">${year}</option>`;
        });

        // Populate Country Filter
        const countryDropdown = document.getElementById('countryFilter');
        countryDropdown.innerHTML = '<option value="">Select Country</option>';
        data.countries.forEach(country => {
            countryDropdown.innerHTML += `<option value="${country}">${country}</option>`;
        });

        // Populate Age Group Filter
        const ageDropdown = document.getElementById('ageFilter');
        ageDropdown.innerHTML = '<option value="">Select Age Group</option>';
        data.ages.forEach(age => {
            ageDropdown.innerHTML += `<option value="${age}">${age}</option>`;
        });

        // Populate Sex Filter
        const sexDropdown = document.getElementById('sexFilter');
        sexDropdown.innerHTML = '<option value="">Select Sex</option>';
        data.sexes.forEach(sex => {
            sexDropdown.innerHTML += `<option value="${sex}">${sex}</option>`;
        });

        console.log('Dropdowns populated successfully!');
    } catch (error) {
        console.error('Error fetching dropdown data:', error);
    }
}

$(document).ready(function() {
    // Function to fetch updated dashboard data
    function fetchDashboardData() {
        const year = $('#yearFilter').val();
        const country = $('#countryFilter').val();
        const age = $('#ageFilter').val();
        const sex = $('#sexFilter').val();

        $.ajax({
            type: 'POST',
            url: 'get_dashboard_data.php',
            data: { year: year, country: country, age: age, sex: sex },
            success: function(response) {
                const data = JSON.parse(response);

                // Update the dashboard values
                $('#totalSuicides').text(data.totalSuicides);
                $('#totalCountries').text(data.totalCountries);
                $('#totalYears').text(data.totalYears);

                // You can add more code here to update the charts with data.suicideTrends, data.gdpVsSuicide, and data.topCountries
            },
            error: function() {
                alert('Failed to fetch data');
            }
        });
    }

    // Event listener for filter changes
    $('#yearFilter, #countryFilter, #ageFilter, #sexFilter').change(function() {
        fetchDashboardData();
    });

    // Fetch initial data when the page loads
    fetchDashboardData();
});

// Initialize the dropdown population
fetchDropdownData();
