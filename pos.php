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

$defaultImageURL = "https://freepngimg.com/thumb/tools/23129-7-tools.png";

$products = [];
$query = "SELECT id, productname, price, quantity FROM inventory";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'name' => $row['productname'],
            'value' => $row['price'],
            'quantity' => $row['quantity'],
            'image' => $defaultImageURL
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS</title>
    <style>
                body {
  color: #56514B;
  font-size: 16px;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  align-items: center; /* Center horizontally */
}
body.bill {
  flex-direction: column;
}

.search-bar {
  margin-top: 1rem;
  text-align: center;
}
.search-bar input {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.customer-name {
  margin-top: 1rem;
}
.customer-name input {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.products {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  max-width: 1200px; /* Adjust as needed */
  flex: 0 0 calc(70% - 1rem);
}
.product {
  background-color: #E7E5DD;
  color: black;
  border-radius: 10px;
  box-sizing: border-box;
  padding: 1rem;
  position: relative;
  margin: 1rem;
  flex: 0 0 20%;
  text-align: center;
  cursor: pointer;
}   

.bill {
  /* ... (your existing .bill styles) ... */
  flex: 0 0 calc(30% - 1rem); /* Adjust the calculation as needed */
}

section.products {
  flex: 0 0 70%;
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: space-between;
}
section.products > div {
  background-color: #E7E5DD;
  border-radius: 10px;
  box-sizing: border-box;
  padding: 1rem;
  position: relative;
  margin: 1rem;
  flex: 0 0 20%;
  text-align: center;
  cursor: pointer;
}
.product-quantity {
    font-size: 14px;
    color: black;
}

/* Solving click event bug */
section.products > div:after {
  content: '';
  display: block;
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
}
section.products > div > img {
  max-height: 5rem;
  max-width: 5rem;
  margin-bottom: 1rem;
}
section.products > div > p {
  margin: 0;
  padding: 0;
}       
.hidden {
	display: none;
}
.back-button {
        position: absolute;
        top: 10px;
        left: 10px;
    }

    .back-button a {
        display: inline-block;
        padding: 10px 15px;
        background-color: #333;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    }

    .back-button a:hover {
        background-color: #555;
    }

    .checkout{
        position: absolute;
    }

    .checkout a {
        display: inline-block;
        padding: 10px 15px;
        background-color: #333;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    }

    .checkout a:hover {
        background-color: #555;
    }

    .product.disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
    .content-container {
        display: flex;
        justify-content: space-between;
        max-width: 1200px; /* Adjust as needed */
        margin: 0 auto;
}
    </style>
</head>
<body>
<div class="search-bar">
    <!-- Search bar -->
    <input type="text" name="search" id="searchInput" placeholder="Search items">
</div>

<div class="content-container">
    <section class="products" style="max-height: 80vh; overflow-y: auto;">
        <p id="noItemsFound" style="display: none;">No items found.</p>
        <?php foreach ($products as $product) { ?>
            <div class="product <?php echo $product['quantity'] === 0 ? 'disabled' : ''; ?>"
                 data-index="<?php echo $product['id']; ?>"
                 data-name="<?php echo $product['name']; ?>"
                 data-value="<?php echo $product['value']; ?>"
                 data-quantity="<?php echo $product['quantity']; ?>"
                 data-image="<?php echo $product['image']; ?>">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                <p class="product-name"><?php echo $product['name']; ?></p>
                <p class="product-value">₱<?php echo $product['value']; ?></p>
                <p class="product-quantity">Quantity: <?php echo $product['quantity']; ?></p>
            </div>
        <?php } ?>
    </section>

    <section class="bill">
        <div class="bill-products">
            <h2>Selected Items</h2>
        </div>
        <div class="bill-client">
            <form method="POST" action="./bill.php">
                <div class="hidden">
                    <label for="products">Products</label>
                    <input type="text" name="products" id="products" placeholder="Products ID" value="">
                </div>
                <div class="customer-name">
                    <input type="text" name="name" id="name" placeholder="Customer Name" required>
                </div>
                <div class="checkout">
                    <br><input type="submit" value="Print">
                </div>
                <div class="back-button">
                    <a href="index.php">Home</a>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
    const products = document.querySelectorAll('.product');
    const selectedItemsContainer = document.querySelector('.bill-products');

// Update the event listener to handle increasing quantity
products.forEach(product => {
    product.addEventListener('click', () => {
        const index = product.dataset.index;
        const name = product.dataset.name;
        const value = parseFloat(product.dataset.value);
        let quantity = parseInt(product.dataset.quantity);

        if (quantity > 0) {
            const selectedProduct = document.querySelector(`.selected-product[data-index="${index}"]`);

            if (selectedProduct) {
                // Item is already selected, increment quantity
                const quantityInput = selectedProduct.querySelector('.selected-product-quantity-input');
                quantityInput.value = parseInt(quantityInput.value) + 1;
                quantity = parseInt(quantityInput.value);
            } else {
                // Item is not selected yet, create a new entry
                const selectedProduct = document.createElement('div');
                selectedProduct.classList.add('selected-product');
                selectedProduct.dataset.index = index;
                selectedProduct.innerHTML = `
                    <span class="selected-product-info">
                        ${name} - ₱${value.toFixed(2)}
                    </span>
                    <input type="number" class="selected-product-quantity-input" min="1" value="1">
                `;
                selectedItemsContainer.appendChild(selectedProduct);

                const quantityInput = selectedProduct.querySelector('.selected-product-quantity-input');
                quantityInput.addEventListener('change', () => {
                    quantity = parseInt(quantityInput.value);
                });
            }

            // Update the remaining quantity in the dataset
            product.dataset.quantity = quantity - 1;

            let productsInput = document.querySelector('section.bill #products');
            if (productsInput.value === '') {
                productsInput.value += index;
            } else {
                productsInput.value += ',' + index;
            }
        }
    });
});

</script>
</body>
</html>
