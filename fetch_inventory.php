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

// Get the search term and current page from the query string
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$itemsPerPage = 13; // Set the number of items per page

// Calculate the OFFSET for the SQL query
$offset = ($page - 1) * $itemsPerPage;

// Prepare and execute the SQL query with the search term filter and pagination
$query = "SELECT * FROM inventory WHERE productname LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$searchTermWithWildcards = "%$searchTerm%"; // Add wildcards for partial matching
$stmt->bind_param("sii", $searchTermWithWildcards, $itemsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>ID</th><th>Item Name</th><th>Category</th><th>Quantity</th><th>Price</th></tr>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr class="inventory-row">';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['productname'] . '</td>';
        echo '<td>' . $row['category'] . '</td>';
        echo '<td>' . $row['quantity'] . '</td>';
        echo '<td>' . $row['price'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo "No inventory data available.";
}

$stmt->close();
$conn->close();
?>
