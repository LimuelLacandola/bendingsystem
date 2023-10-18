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

if (isset($_POST['restock'])) {
    $itemId = $_POST['item_id'];
    
    // Restock the item by adding 50 to its quantity
    $query = "UPDATE inventory SET quantity = quantity + 50 WHERE id = $itemId";
    
    if ($conn->query($query) === TRUE) {
        // Redirect back to the restock page after successful restocking
        header("Location: restock.php");
        exit;
    } else {
        echo "Error restocking item: " . $conn->error;
    }
}

$conn->close();
?>
