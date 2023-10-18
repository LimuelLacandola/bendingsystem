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

$totalProducts = 0;
$lowStockItems = [];

// Get the total number of products
$totalProductsQuery = "SELECT COUNT(*) as total_products FROM inventory";
$totalProductsResult = $conn->query($totalProductsQuery);

if ($totalProductsResult->num_rows > 0) {
    $row = $totalProductsResult->fetch_assoc();
    $totalProducts = $row['total_products'];
}

// Get low stock items (quantity less than or equal to 20)
$lowStockQuery = "SELECT * FROM inventory WHERE quantity <= 20";
$lowStockResult = $conn->query($lowStockQuery);

if ($lowStockResult->num_rows > 0) {
    while ($row = $lowStockResult->fetch_assoc()) {
        $lowStockItems[] = [
            'name' => $row['productname'],
            'quantity' => $row['quantity']
        ];
    }
}

$data = [
    'totalProducts' => $totalProducts,
    'lowStockItems' => $lowStockItems
];

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
