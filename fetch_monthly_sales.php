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

// Fetch monthly sales data
$monthlySalesData = array();

// Modify the SQL query to match your database structure
$query = "SELECT DATE_FORMAT(transaction_date, '%Y-%m') AS month, COUNT(*) AS total_sales 
          FROM transaction_history
          WHERE DATE(transaction_date) >= DATE(NOW() - INTERVAL 6 MONTH)
          GROUP BY month
          ORDER BY month";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $monthlySalesData[$row['month']] = (int) $row['total_sales'];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($monthlySalesData);
?>
