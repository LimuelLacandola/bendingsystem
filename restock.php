<!DOCTYPE html>
<html>
<head>
    <title>Restock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        .navigation {
            background-color: #333;
            color: #fff;
            width: 250px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
            padding: 20px;
            margin-top: 50px;
        }

        .content h2 {
            margin-top: 0;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
        }

        .card {
            border: 1px solid #ccc;
            padding: 15px;
            margin-right: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            width: 200px;
            height: 160px;
        }

        .card h3 {
            margin-top: 0;
        }

        .card p {
            margin: 0;
            margin-bottom: 10px;
        }

        .card button {
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 8px 16px;
            cursor: pointer;
        }

        .card button:hover {
            background-color: #666;
        }

        .navigation a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
        }

        .navigation a:not(:last-child) {
            margin-bottom: 25px;
        }

        .navigation a:hover {
            text-decoration: underline;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to fetch and update low stock items
            function updateLowStockItems() {
                $.ajax({
                    url: 'fetch_low_stock_items.php', // Create this PHP file to fetch low stock items
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Clear the existing low stock items
                        $('.content').empty();

                        // Iterate through the fetched items and update the page
                        if (data.length > 0) {
                            $.each(data, function(index, item) {
                                var card = $('<div class="card">');
                                card.append('<h3>' + item.productname + '</h3>');
                                card.append('<p>Category: ' + item.category + '</p>');
                                card.append('<p>Quantity: ' + item.quantity + '</p>');
                                card.append('<p>Price: ' + item.price + '</p>');

                                // Add a RESTOCK ITEM button for each low stock item
                                var form = $('<form method="post" action="restock_process.php">');
                                form.append('<input type="hidden" name="item_id" value="' + item.id + '">');
                                var restockButton = $('<button type="submit" name="restock">RESTOCK ITEM</button>');
                                form.append(restockButton);
                                card.append(form);

                                $('.content').append(card);
                            });
                        } else {
                            // Display a message if no low stock items found
                            $('.content').append('<p>No low stock items found.</p>');
                        }
                    }
                });
            }

            // Call the updateLowStockItems function initially
            updateLowStockItems();

            // Periodically update low stock items every 5 seconds (adjust as needed)
            setInterval(updateLowStockItems, 5000); // 5 seconds interval
        });
    </script>
</head>
<body>
<div class="navigation">
    <h2>San and Elisse Bending Shop</h2>
    <a href="index.php">Home</a>
    <a href="inventory.php">Inventory</a>
    <a href="pos.php">Point of Sale</a>
    <a href="#">Inventory Report</a>
    <a href="sales_report.php">Sales Report</a>
    <a href="transaction_history.php">Transaction History</a>
	<a href="restock.php">Restock</a>
	<a href="supplier.php">Supplier</a>
    <a href="logout.php">Logout</a>
</div>
<div class="content">
    <h2>Restock</h2>
	<div class="card-container">

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

    // Query to retrieve low stock items
    $lowStockThreshold = 20; 
    $query = "SELECT * FROM inventory WHERE quantity < $lowStockThreshold";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="card">';
            echo '<h3>' . $row['productname'] . '</h3>';
            echo '<p>Category: ' . $row['category'] . '</p>';
            echo '<p>Quantity: ' . $row['quantity'] . '</p>';
            echo '<p>Price: ' . $row['price'] . '</p>';
            
            // Add a RESTOCK ITEM button for each low stock item
            echo '<form method="post" action="restock_process.php">';
            echo '<input type="hidden" name="item_id" value="' . $row['id'] . '">';
            echo '<button type="submit" name="restock">RESTOCK ITEM</button>';
            echo '</form>';
            
            echo '</div>';
        }
    } else {
        echo '<p>No low stock items found.</p>';
    }

    $conn->close();
    ?>
	</div>
</div>
</body>
</html>
