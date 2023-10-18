<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bendingsystem";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $id = $_POST['id'];
    $productname = $_POST['productname'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Update data in inventory table
    $query = "UPDATE inventory SET productname = ?, category = ?, quantity = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiii", $productname, $category, $quantity, $price, $id);

    if ($stmt->execute()) {
        header("Location: inventory.php"); // Redirect back to inventory page
    } else {
        echo "Error updating item: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
