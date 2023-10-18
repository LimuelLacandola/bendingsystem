<!DOCTYPE html>
<html>
<head>
    <title>Supplier Information</title>
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
    <a href="logout.php">Logout</a>
</div>
</div>
<div class="content">
    <h2>Supplier Information</h2>
    <input type="text" id="searchBox" placeholder="Search suppliers">
    <table id="supplierTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier Name</th>
                <th>Email Address</th>
                <th>Contact No</th>
            </tr>
        </thead>
        <tbody id="supplierData"></tbody>
    </table>
</div>

<script>
    // Function to fetch and display supplier data
    function fetchSuppliers() {
        const search = document.getElementById("searchBox").value;
        const supplierData = document.getElementById("supplierData");

        // Send an AJAX request to fetch_supplier.php
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `fetch_supplier.php?search=${search}`);
        xhr.send();

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                // Clear the supplier data
                supplierData.innerHTML = '';

                // Loop through the data and populate the table
                response.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.supplier_name}</td>
                        <td>${item.email_address}</td>
                        <td>${item.contact_no}</td>
      
                    `;
                    supplierData.appendChild(row);
                });
            }
        };
    }

    // Fetch supplier data on page load
    fetchSuppliers();

    // Add an event listener to the search input
    const searchBox = document.getElementById("searchBox");
    searchBox.addEventListener("input", fetchSuppliers);
</script>
</body>
</html>
