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

// Fetch weekly sales data
$weeklySalesData = array();

// Modify the SQL query to match your database structure
$query = "SELECT YEARWEEK(transaction_date) AS week, COUNT(*) AS total_sales 
          FROM transaction_history
          WHERE DATE(transaction_date) >= DATE(NOW() - INTERVAL 12 WEEK)
          GROUP BY week
          ORDER BY week";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $weeklySalesData[$row['week']] = (int) $row['total_sales'];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($weeklySalesData);
?>
