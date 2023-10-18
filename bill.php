<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>

    </style>
</head>
<body>
    <section class="receipt">
        <div class="receipt-header">
           <center> <h2>San and Elisse Bending Shop</h2><br></center>
            <center><p>Date: <?php echo date('Y-m-d H:i:s'); ?></p></center>
            <?php
if (isset($_POST['products'])) {
    $productIdsArray = explode(',', $_POST['products']);
} else {
    // Handle the case when no products are selected
    $productIdsArray = [];
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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['products'])) {
        $productIdsArray = explode(',', $_POST['products']);

        // Deduct quantities from inventory and calculate total price
        $totalPrice = 0; // Initialize total price
        foreach ($productIdsArray as $productId) {
            $updateQuery = "UPDATE inventory SET quantity = quantity - 1 WHERE id = '$productId' AND quantity > 0";
            $conn->query($updateQuery);

            // Get product price and add to total price
            $priceQuery = "SELECT price FROM inventory WHERE id = '$productId'";
            $priceResult = $conn->query($priceQuery);
            if ($priceResult->num_rows > 0) {
                $productPrice = $priceResult->fetch_assoc()['price'];
                $totalPrice += $productPrice;
            }
        }

        // Insert transaction history
        $customerName = $_POST['name'];
        $transactionDate = date('Y-m-d H:i:s');
        $insertQuery = "INSERT INTO transaction_history (customer_name, transaction_date, total_price) VALUES ('$customerName', '$transactionDate', $totalPrice)";
        $conn->query($insertQuery);
    }
}



$products = [];
$totalPrice = 0;

foreach ($productIdsArray as $productId) {
    $query = "SELECT * FROM inventory WHERE id = '$productId'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (isset($products[$productId])) {
            // Item already exists, increment quantity
            $products[$productId]['quantity']++;
        } else {
            // Item doesn't exist yet, create a new entry
            $products[$productId] = [
                'name' => $row['productname'],
                'value' => $row['price'],
                'quantity' => 1
            ];
        }
        
        $totalPrice += $row['price'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <style>
   /* Reset default margin and padding */
/* Reset default margin and padding */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}

.receipt {
    max-width: 400px;
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.header {
    text-align: center;
    margin-bottom: 20px;
}

.header h1 {
    font-size: 24px;
}

.content {
    border-top: 1px solid #ccc;
    padding-top: 20px;
}

.product {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.product-name {
    flex-grow: 1;
}

.product-value {
    font-weight: bold;
}

.total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding-top: 10px;
    border-top: 1px solid #ccc;
}

.total span:first-child {
    font-weight: bold;
}

.total-value {
    font-weight: bold;
}

.product-headers {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #ccc;
}

.product-header {
    text-align: left;
}

.product-quantity {
    flex-grow: 2;
    text-align: center;
}

.product-value {
    text-align: right;
}

.customer-info {
    display: flex;
    align-items: center;
    margin-top: 10px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ccc;
}

.customer-info strong {
    margin-right: 10px;
}

@media print {
            .print-button-container {
                display: none;
            }
        }
        .print-button-container {
            text-align: center;
            margin-top: 20px;
        }

        .print-button {
        text-decoration: none;
        color: #fff;
        background-color: #333;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        display: inline-block;
        
    }

    /* Style for the print button when hovered */
    .print-button:hover {
        background-color: #333;
    }

    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h3>San and Elisse Bending Shop</h3>
			<h5> Cabuyao, Laguna </h5>
			<h5> +639 12 345 6789 </h5>
        </div>
  
        <div class="content">
    <center><h3>Customer Details</h3></center>
    <div class="customer-info">
        <strong><span>Customer name:</strong></span><p><span><?php echo $_POST['name']; ?></span></p>
    </div>

    <div class="product-headers">
        <span class="product-header">Product Name</span>
        <span class="product-header">Quantity</span>
        <span class="product-header">Price</span>
    </div>

    <?php foreach ($products as $productId => $product) { ?>
        <div class="product">
            <span class="product-name"><?php echo $product['name']; ?></span>
            <span class="product-quantity">x<?php echo $product['quantity']; ?></span>
            <span class="product-value">₱<?php echo $product['value']; ?></span>
        </div>
    <?php } ?>

    <div class="total">
        <span>Total:</span>
        <span class="total-value">₱<?php echo $totalPrice; ?></span>
    </div>
</div>
    </div>
    

    
<div class="print-button-container">
    <button class="print-button" onclick="printReceipt()">Print Receipt</button>
</div>

<div class="print-button-container">
    <a href="pos.php" class="print-button">Back</a>
</div>


    <script>
        // Function to print the receipt
        function printReceipt() {
            var receiptContent = document.querySelector('.receipt'); // Get the receipt div
            var printWindow = window.open('', '', 'width=600,height=600'); // Open a new window
            printWindow.document.open();
            printWindow.document.write('<html><head><title>Receipt</title></head><body>'); // Create a new HTML document
            printWindow.document.write(receiptContent.innerHTML); // Add the receipt content
            printWindow.document.write('</body></html>');
            printWindow.document.close();

            // Apply a style to hide the button when printing
            printWindow.document.getElementsByTagName('button')[0].style.display = 'none';

            printWindow.print(); // Print the new window
            printWindow.close(); // Close the new window
        }
    </script>
    
</body>
</html>
