<?php
// Database connection (same as your other pages)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bendingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the product ID from the POST request
$product_id = $_POST['product_id'];

// Update the stock (adjust the SQL query as per your database structure)
$query = "UPDATE inventory SET quantity = quantity - 1 WHERE id = $product_id";

if ($conn->query($query) === TRUE) {
    // Retrieve the new quantity after the update
    $new_quantity_query = "SELECT quantity FROM inventory WHERE id = $product_id";
    $result = $conn->query($new_quantity_query);
    $new_quantity = $result->fetch_assoc()['quantity'];

    // Return the new quantity as JSON
    echo json_encode(['success' => true, 'new_quantity' => $new_quantity]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>
