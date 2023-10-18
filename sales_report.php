<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        .navigation {
            background-color: #333;
            color: #fff;
            width: 250px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .navigation a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
        }
        .navigation a:not(:last-child) {
            margin-bottom: 25px;
        }
        .navigation a:hover {
            text-decoration: underline;
        }
        canvas {
            margin-top: 20px;
        }
        /* Style for the dropdown container */
        .dropdown {
            position: relative;
            display: inline-block;
            vertical-align: middle;
            width: 150px; /* Adjust the width as needed */
        }

        /* Style for the select element */
        .styled-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 1px solid #ccc;
            padding: 8px 20px 8px 10px;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
        }

        /* Style for the select arrow */
        .styled-select::after {
            content: "\25BC";
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="navigation">
        <h2>San and Elisse Bending Shop</h2>
        <a href="index.php">Home</a>
        <a href="inventory.php">Inventory</a>
        <a href="pos.php">Point of Sale</a>
        <a href="sales_report.php">Sales Report</a>
        <a href="transaction_history.php">Transaction History</a>
		<a href="restock.php">Restock</a>
		<a href="supplier.php">Supplier</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
    <h2>Sales Report</h2>
    <div>
    <center><div class="dropdown">
        <select id="toggleSales" class="styled-select">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
        </select>
    </div>
</div>
    </center>

    <canvas id="salesChart"></canvas>
        </div>
        <?php
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "bendingsystem";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to retrieve sales data
        $query = "SELECT transaction_date FROM transaction_history";
        $result = $conn->query($query);

        // Initialize an array to store daily transaction counts
        $dailyTransactionCounts = array_fill(0, 7, 0);

        // Check if there are sales records
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $transactionDate = new DateTime($row['transaction_date']);
                $dayOfWeek = $transactionDate->format('w'); // 0 (Sun) to 6 (Sat)
                $dailyTransactionCounts[$dayOfWeek]++;
            }
        }

        // Close the database connection
        $conn->close();
        ?>

<script>
    var salesChart;

    function fetchAndRenderData(selectedOption) {
        var url = '';
        if (selectedOption === 'daily') {
            url = 'fetch_daily_sales.php';
        } else if (selectedOption === 'weekly') {
            url = 'fetch_weekly_sales.php';
        } else if (selectedOption === 'monthly') {
            url = 'fetch_monthly_sales.php';
        }

        fetch(url)
            .then(response => response.json())
            .then(salesData => {
                if (salesChart) {
                    salesChart.destroy();
                }

                var ctx = document.getElementById('salesChart').getContext('2d');
                salesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(salesData),
                        datasets: [{
                            label: selectedOption === 'daily' ? 'Daily Sales' :
                                (selectedOption === 'weekly' ? 'Weekly Sales' : 'Monthly Sales'),
                            data: Object.values(salesData),
                            backgroundColor: 'rgba(51, 51, 51, 0.6)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Transactions'
                                }
                            }
                        }
                    }
                });
            });
    }

    var toggleSales = document.getElementById('toggleSales');
    toggleSales.addEventListener('change', function() {
        fetchAndRenderData(this.value);
    });

    // Initial chart render
    fetchAndRenderData(toggleSales.value);
</script>

    </div>
</body>
</html>
