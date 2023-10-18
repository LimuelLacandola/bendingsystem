<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

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

// Get the total number of sales today
$currentDate = date("Y-m-d"); // Get the current date in the format YYYY-MM-DD
$totalTransactions = 0;

// Query to get the total number of sales for today
$totalTransactionsQuery = "SELECT COUNT(*) as inventory FROM transaction_history WHERE DATE(transaction_date) = '$currentDate'";
$totalTransactionsResult = $conn->query($totalTransactionsQuery);

if ($totalTransactionsResult->num_rows > 0) {
    $row = $totalTransactionsResult->fetch_assoc();
    $totalTransactions = $row['inventory'];
}

// Get the total amount of all transactions today
$totalSales = 0;

// Query to calculate the total amount of transactions for today
$totalSalesQuery = "SELECT SUM(total_price) as inventory FROM transaction_history WHERE DATE(transaction_date) = '$currentDate'";
$totalSalesResult = $conn->query($totalSalesQuery);

if ($totalSalesResult->num_rows > 0) {
    $row = $totalSalesResult->fetch_assoc();
    $totalSales = $row['inventory'];
}



$conn->close();

// Check if there are low stock items before sending the email
if (!empty($lowStockItems)) {
    require 'PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls'; // Use 'tls' instead of 'ssl'
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587; // Port for TLS
    $mail->isHTML();
    $mail->Username = 'lacandolalimuelfelisan@gmail.com';
    $mail->Password = ''; // Replace with your Gmail App Password
    $mail->SetFrom('lacandolalimuelfelisan@gmail.com');
    $mail->Subject = 'Low Stock!';
    
    // Construct the email body with low stock items
    $emailBody = 'The following items are low on stock and need to be restocked:<br>';
    foreach ($lowStockItems as $item) {
        $emailBody .= "Product: {$item['name']}, Quantity: {$item['quantity']}<br>";
    }
    
    $mail->Body = $emailBody;
    $mail->AddAddress('lacandola.l.bsinfotech@gmail.com');
    
    if ($mail->Send()) {
        // Email sent successfully
    } else {
        // Email sending failed
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Homepage</title>
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
        /* Additional styles for the cards */
        .cards-container {
            display: flex;
            gap: 20px;
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
        }
        .card {
            background-color: #f7f7f7;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            width: 35vh;
            display: inline-block;
            overflow: hidden; /* Prevent overflow content from displaying */
        }
        .card-header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .card-body {
            padding: 10px;
            max-height: 100px; /* Limit the height of the card body */
            overflow: auto; /* Add scrollbars if content overflows */
        }
        .low-stock {
            color: red;
            font-weight: bold;
            margin-bottom: 5px; /* Add some spacing between each low-stock item */
            white-space: nowrap; /* Prevent text wrapping */
            overflow: hidden; /* Hide any overflow text */
            text-overflow: ellipsis; /* Display ellipsis (...) for overflow text */
        }
		.black-link {
		color: black;
		text-decoration: none; /* Remove underline */
}
    </style>
</head>
<body>
    <div class="navigation">
        <h2>San and Elisse Bending Shop</h2>
        <a href="index.php">Home</a>
        <a href="inventory.php">Inventory</a>
        <a href="pos.php">Point of Sale</a>
        <a href="sales_report.php">Sales Report</a>
        <a href="transaction_history.php">Transaction History</a>
		<a href="restock.php">Restock</a>
		<a href="supplier.php">Supplier</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
        <p>This is the homepage. Only logged-in users can see this.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nec leo in sapien laoreet rhoncus eu non augue.</p>

        <div class="cards-container">
		<a href = "inventory.php" class="black-link">
            <div class="card" id="totalProductsCard">
                <div class="card-header">
                 <center>Total Number of Products</center>
                </div>
                <div class="card-body">
                    <strong><center><p><?php echo $totalProducts; ?> PRODUCTS</p></center></strong>
                </div>
            </div>
			</a>

			<a href = "restock.php" class="black-link">
            <div class="card" id="lowStockItemsCard">
                <div class="card-header">
                    <center>Low Stock Items</center>
                </div>
                <div class="card-body">
                    <?php if (!empty($lowStockItems)) { ?>
                        <?php foreach ($lowStockItems as $item) { ?>
                            <center><p class="low-stock"><?php echo $item['name']; ?> <?php echo $item['quantity']; ?> pcs left.</p></center>
                        <?php } ?>
                    <?php } else { ?>
                        <center><p>No low stock items.</p></center>
                    <?php } ?>
                </div>
            </div> 
			</a>
			
			<a href = "transaction_history.php" class="black-link">
			    <div class="card" id="totalTransactionsCard">
        <div class="card-header">
           <center> Number of Transactions Today</center>
        </div>
        <div class="card-body">
            <strong><center><p><?php echo $totalTransactions; ?> TRANSACTIONS</p></center></strong>
        </div>
    </div>
	</a>
	
		<a href = "transaction_history.php" class="black-link">
	    <div class="card" id="totalAmountCard">
        <div class="card-header">
            <center>  Total Amount of Sales Today </center> 
        </div>
        <div class="card-body">
            <strong><center><p><?php echo '₱' . number_format($totalSales, 2); ?></p></center></strong>
        </div>
    </div>
	</a>
	
	
			
        </div>
    </div>

    <script>
        // Function to update total products and low stock items
        function updateData() {
            fetch('fetch_index.php')
                .then(response => response.json())
                .then(data => {
                    // Update total products card
                    document.getElementById('totalProductsCard').innerHTML = `
                        <div class="card-header">
                            <center>Total Number of Products</center>
                        </div>
                        <div class="card-body">
                            <strong><center><p>${data.totalProducts} PRODUCTS</p></center></strong>
                        </div>
                    `;

                    // Update low stock items card
                    const lowStockItemsCard = document.getElementById('lowStockItemsCard');
                    lowStockItemsCard.innerHTML = `
                        <div class="card-header">
                            <center>Low Stock Items</center>
                        </div>
                        <div class="card-body">
                            ${data.lowStockItems.length > 0 ? data.lowStockItems.map(item => `<center><p class="low-stock">${item.name} ${item.quantity} pcs left.</p></center>`).join('') : '<center><p>No low stock items.</p></center>'}
                        </div>
                    `;
					document.getElementById('totalTransactions').textContent = `${data.totalTransactions} SALES`;
                });
        }

        // Initial data update
        updateData();

        // Set up interval to periodically update the data (adjust as needed)
        setInterval(updateData, 5000); // Update every 5 seconds
    </script>
</body>
</html>
