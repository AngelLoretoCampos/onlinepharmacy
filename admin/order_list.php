<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Delete order
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['order_id'])) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start a transaction
        $pdo->beginTransaction();

        // Delete associated order items first
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = :order_id");
        $stmt->bindValue(':order_id', $_GET['order_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Then delete the order
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :order_id");
        $stmt->bindValue(':order_id', $_GET['order_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        header("Location: order_list.php");
        exit;

    } catch (PDOException $e) {
        // Rollback the transaction in case of failure
        $pdo->rollBack();

        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
}

// Pagination variables
$perPage = 10; // Number of items per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Current page number
$start = ($page - 1) * $perPage; // Offset for the query

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch orders
    if ($searchTerm) {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_name LIKE :searchTerm OR ref_code LIKE :searchTerm ORDER BY order_date DESC LIMIT :start, :perPage");
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM orders ORDER BY order_date DESC LIMIT :start, :perPage");
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total orders for pagination
    if ($searchTerm) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE customer_name LIKE :searchTerm OR ref_code LIKE :searchTerm");
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    }
    $stmt->execute();
    $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalOrders / $perPage);

} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'inc/sidebar.php'; ?>

<head>
    <meta charset="UTF-8">
    <title>Order List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100 ml-64">

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-semibold mb-4">Order List</h1>

        <!-- Search Bar -->
        <div class="mb-4 flex justify-end items-center">
            <div class="flex">
                <input type="text" id="search" name="search" placeholder="Search.." class="border rounded-md px-4 py-2 w-64" value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="button" id="searchButton" class="bg-blue-500 text-white hover:bg-blue-300 px-4 py-2 rounded-md ml-2">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="w-1/5 text-center py-2 px-4">Order Date</th>
                        <th class="w-1/5 text-center py-2 px-4">Total Amount</th>
                        <th class="w-1/5 text-center py-2 px-4">Status</th>
                        <th class="w-1/5 text-center py-2 px-4">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo $order['order_date']; ?></td>
                            <td class="border px-4 py-2"><?php echo $order['total_amount']; ?></td>
                            <td class="border px-4 py-2"><?php echo $order['order_status']; ?></td>
                            <td class="border px-4 py-2 text-center">
                                <a href="view_order.php?order_id=<?php echo $order['id']; ?>" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-eye"></i></a>
                                <a href="order_list.php?action=delete&order_id=<?php echo $order['id']; ?>" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between mt-4">
            <a href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Prev</a>
            <div>Page <?php echo $page; ?> of <?php echo $totalPages; ?></div>
            <a href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Next</a>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const searchButton = document.getElementById('searchButton');

        searchButton.addEventListener('click', function() {
            const searchTerm = searchInput.value;
            window.location.href = 'order_list.php?search=' + searchTerm;
        });
    </script>

</body>

</html>
