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
    $productname = $_POST['productname'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Insert data into inventory table
    $query = "INSERT INTO inventory (productname, category, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $productname, $category, $quantity, $price);

    if ($stmt->execute()) {
        header("Location: inventory.php"); // Redirect back to inventory page
    } else {
        echo "Error adding item: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
