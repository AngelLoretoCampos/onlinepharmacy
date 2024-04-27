<?php
// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Include your database connection file
include_once "../database/dbconnection.php";

// Placeholder for sales data
$sales_data = [];

// Initialize start and end date variables
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Pagination
$perPage = 10; // Number of items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1; // Current page number

// Retrieve sales data from the database
try {
    // Connect to the database
    $conn = new PDO("mysql:host=localhost;dbname=onlinepharmacy_db", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL statement to fetch sales data with additional details
    $sql = "SELECT DATE(o.order_date) as date, 
                   CONCAT(u.firstname, ' ', u.lastname) as full_name,
                   GROUP_CONCAT(DISTINCT p.product_name SEPARATOR '<br>') as product_names,
                   GROUP_CONCAT(DISTINCT oi.quantity SEPARATOR '<br>') as quantities,
                   GROUP_CONCAT(DISTINCT oi.price SEPARATOR '<br>') as prices,
                   o.total_amount,
                   o.order_status
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN users u ON o.user_id = u.id
            JOIN products p ON oi.product_id = p.id
            WHERE o.order_status = 'Delivered'";
    
    if ($start_date && $end_date) {
        $sql .= " AND DATE(o.order_date) BETWEEN :start_date AND :end_date";
    }

    $sql .= " GROUP BY DATE(o.order_date), full_name, o.total_amount, o.order_status";

    // Calculate the offset for the query
    $offset = ($page - 1) * $perPage;
    $sql .= " LIMIT $perPage OFFSET $offset";

    $stmt = $conn->prepare($sql);

    if ($start_date && $end_date) {
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
    }

    $stmt->execute();

    // Fetch all rows
    $sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total number of rows
    $stmt = $conn->query("SELECT COUNT(*) as total FROM ($sql) as countQuery");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Calculate total pages
    $totalPages = ceil($total / $perPage);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;

include 'inc/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 ml-64">

<div class="container mt-10 py-10 px-6   bg-white rounded-lg shadow-md">
    <div class="mx-auto">
        <h1 class="text-2xl font-bold mb-4">Sales Report</h1>
        
        <!-- Filter Section -->
        <div class="mb-4  rounded-md p-4">
            <form action="" method="get">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="ml-2 border rounded-md p-2">

                <label for="end_date" class="ml-4">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="ml-2 border rounded-md p-2">

                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md ml-4">Filter</button>
            </form>

        </div>

        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="border px-4 py-2">Date</th>
                        <th class="border px-4 py-2">Customer Name</th>
                        <th class="border px-4 py-2">Product Names</th>
                        <th class="border px-4 py-2">Quantities</th>
                        <th class="border px-4 py-2">Prices</th>
                        <th class="border px-4 py-2">Total Amount</th>
                        <th class="border px-4 py-2">Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales_data as $sale): ?>
                    <tr class="bg-gray-100">
                        <td class="border px-4 py-2"><?php echo $sale['date']; ?></td>
                        <td class="border px-4 py-2"><?php echo $sale['full_name']; ?></td>
                        <td class="border px-4 py-2"><?php echo nl2br($sale['product_names']); ?></td>
                        <td class="border px-4 py-2">
                            <?php 
                                $quantities = explode('<br>', $sale['quantities']);
                                foreach ($quantities as $quantity) {
                                    echo $quantity . ' pc(s)<br>';
                                }
                            ?>
                        </td>
                        <td class="border px-4 py-2">
                            <?php 
                                $prices = explode('<br>', $sale['prices']);
                                foreach ($prices as $price) {
                                    echo '₱' . $price . '<br>';
                                }
                            ?>
                        </td>
                        <td class="border px-4 py-2">₱<?php echo $sale['total_amount']; ?></td>
                        <td class="border px-4 py-2"><?php echo $sale['order_status']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="flex justify-between mt-4">
                <a href="?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Prev</a>
                <div>Page <?php echo $page; ?> of <?php echo $totalPages; ?></div>
                <a href="?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Next</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
