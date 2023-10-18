<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
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
        table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #f2f2f2;
        }
		
		    .pagination {
            display: flex;
            list-style: none;
            padding: 0;
			justify-content: center;
		
			
        }

        .pagination li {
            margin-right: 5px;
        }

        .pagination button {
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
			margin-right: 10px;
			text-align: center;
			
        }

        .pagination button:hover {
            background-color: #666;
        }
		
		        /* Highlight low stock quantities in red */
        .low-stock {
            color: red;
        }

        /* Highlight mid stock quantities in orange */
        .mid-stock {
            color: orange;
        }

        /* Highlight high stock quantities in green */
        .high-stock {
            color: green;
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
    <h2>Inventory</h2>
    <input type="text" id="searchBox" placeholder="Search items">
    <div id="inventoryTable"></div>
    <ul class="pagination" id="pagination">
        <li><button id="prevPage">Back</button></li>
        <li><button id="nextPage">Next</button></li>
    </ul>
    <script>
        let currentPage = 1; // Current page
        const itemsPerPage = 13; // Number of items per page

        // Function to update the inventory table with pagination
        function updateInventory(searchTerm, page) {
            const inventoryTable = document.getElementById('inventoryTable');

            // Send an AJAX request to fetch inventory data with the search term and page
            fetch(`fetch_inventory.php?search=${searchTerm}&page=${page}`)
                .then(response => response.text())
                .then(data => {
                    inventoryTable.innerHTML = data;
                });

            // Hide the "Previous" button when on the first page
            if (page === 1) {
                document.getElementById('prevPage').style.display = 'none';
            } else {
                document.getElementById('prevPage').style.display = 'inline-block';
            }
        }

        // Function to handle search box input
        document.getElementById('searchBox').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            currentPage = 1; // Reset to the first page when searching
            updateInventory(searchTerm, currentPage);
        });

        // Function to handle Previous button click
        document.getElementById('prevPage').addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                updateInventory(document.getElementById('searchBox').value, currentPage);
            }
        });

        // Function to handle Next button click
        document.getElementById('nextPage').addEventListener('click', function () {
            currentPage++;
            updateInventory(document.getElementById('searchBox').value, currentPage);
        });

        // Initial table update without search term
        updateInventory('', currentPage);
    </script>
</div>
</body>
</html>
