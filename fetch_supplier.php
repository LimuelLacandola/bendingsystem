<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bendingsystem";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to select all records from the supplier table
$sql = "SELECT * FROM supplier";

// Execute the query
$result = $conn->query($sql);

// Initialize an array to store the data
$data = [];

// Check if there are records
if ($result->num_rows > 0) {
    // Fetch data from each row
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Close the database connection
$conn->close();

// Set the response content type to JSON
header('Content-Type: application/json');

// Return the data as JSON
echo json_encode($data);
?>
