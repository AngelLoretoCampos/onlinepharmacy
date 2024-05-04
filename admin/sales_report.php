<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "onlinepharmacy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to calculate total sales amount of orders with status 'Delivered'
$sql = "SELECT SUM(o.total_amount) AS total_sales_amount
        FROM orders o 
        WHERE o.order_status = 'Delivered'";

// Execute SQL query
$result = $conn->query($sql);

// Fetch the total sales amount
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalSalesAmount = $row["total_sales_amount"];
} else {
    $totalSalesAmount = 0;
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 ml-64">

<div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-semibold mb-4">Sales Report</h1>

    <?php include 'inc/sidebar.php'; ?>

    <!-- Filter Form -->
    <form action="" method="get" class="mb-4">
        <label for="start_date" class="mr-2">Start Date:</label>
        <input type="date" id="start_date" name="start_date" class="px-2 py-1 border rounded">

        <label for="end_date" class="mx-4">End Date:</label>
        <input type="date" id="end_date" name="end_date" class="px-2 py-1 border rounded">

        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Filter</button>
    </form>
    <!-- Total Sales Amount Card -->
    <div class="bg-blue-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md mb-4">
        <div class="flex items-center">
            <div class="text-xl font-bold">
                Total Sales Amount:
            </div>
            <div class="ml-2 text-xl">
                ₱<?php echo number_format($totalSalesAmount, 2); ?>
            </div>
        </div>
    </div>

    
    <table class="table-auto w-full">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="px-4 py-2">Customer Name</th>
                <th class="px-4 py-2">Payment Method</th>
                <th class="px-4 py-2">Total Amount</th>
                <th class="px-4 py-2">Order Date</th>
                <th class="px-4 py-2">Order Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection details
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "onlinepharmacy_db";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $database);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Pagination
            $perPage = 10; // Number of items per page
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1; // Current page number

            // Filter dates
            $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
            $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

            // SQL query to retrieve orders with status 'Delivered' and user details with pagination
            $sql = "SELECT CONCAT(u.firstname, ' ', u.lastname) AS full_name, o.payment_method, o.total_amount, o.order_date, o.order_status 
                    FROM orders o 
                    INNER JOIN users u ON o.user_id = u.id 
                    WHERE o.order_status = 'Delivered'";

            // Add date filter if provided
            if (!empty($startDate) && !empty($endDate)) {
                $sql .= " AND o.order_date BETWEEN '$startDate' AND '$endDate'";
            }
            
            // Execute SQL query
            $result = $conn->query($sql);

            // Calculate total pages
            $totalPages = ceil($result->num_rows / $perPage);

            // Adjust current page if necessary
            if ($page < 1) {
                $page = 1;
            } elseif ($page > $totalPages) {
                $page = $totalPages;
            }

            // Calculate the offset for the query
            $offset = ($page - 1) * $perPage;

            // SQL query with pagination
            $sql .= " ORDER BY o.order_date DESC LIMIT $perPage OFFSET $offset";

            $result = $conn->query($sql);

            // Output data of each row
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td class='border px-4 py-2'>".$row["full_name"]."</td>
                            <td class='border px-4 py-2'>".$row["payment_method"]."</td>
                            <td class='border px-4 py-2'>".$row["total_amount"]."</td>
                            <td class='border px-4 py-2'>".$row["order_date"]."</td>
                            <td class='border px-4 py-2'>".$row["order_status"]."</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='px-4 py-2'>No orders found with status 'Delivered'</td></tr>";
            }

            // Close connection
            $conn->close();
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="flex justify-between mt-4">
        <a href="?start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>&page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Prev</a>
        <div>Page <?php echo $page; ?> of <?php echo $totalPages; ?></div>
        <a href="?start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>&page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Next</a>
    </div>
</div>

</body>
</html>
