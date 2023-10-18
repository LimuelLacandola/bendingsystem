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
$itemsPerPage = 13;

// Calculate the OFFSET for the SQL query
$offset = ($page - 1) * $itemsPerPage;

// Prepare and execute the SQL query with the search term filter and pagination
$query = "SELECT * FROM transaction_history WHERE customer_name LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$searchTermWithWildcards = "%$searchTerm%"; // Add wildcards for partial matching
$stmt->bind_param("sii", $searchTermWithWildcards, $itemsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table id="transactionTable">';
    echo '<tr><th>ID</th><th>Customer Name</th><th>Transaction Date</th><th>Total Price</th></tr>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['customer_name'] . '</td>';
        echo '<td>' . $row['transaction_date'] . '</td>';
        echo '<td>₱' . $row['total_price'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo "No transaction history data available.";
}

$stmt->close();
$conn->close();
