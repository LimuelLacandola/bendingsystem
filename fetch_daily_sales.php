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

// Fetch daily sales data
$dailySalesData = array();

// Modify the SQL query to match your database structure
$query = "SELECT DATE(transaction_date) AS date, COUNT(*) AS total_sales 
          FROM transaction_history
          WHERE DATE(transaction_date) >= DATE(NOW() - INTERVAL 7 DAY)
          GROUP BY DATE(transaction_date)
          ORDER BY date";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dailySalesData[$row['date']] = (int) $row['total_sales'];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($dailySalesData);
?>
