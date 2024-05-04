<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinepharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Pagination variables
$recordsPerPage = 10; // Number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number

// Count total number of orders for the logged-in user
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalRecords = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Calculate total number of pages
$totalPages = ceil($totalRecords / $recordsPerPage);

// Offset calculation for pagination
$offset = ($page - 1) * $recordsPerPage;

// Fetch orders for the logged-in user, ordered by order date descending with pagination
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT ?, ?");
$stmt->bind_param("iii", $user_id, $offset, $recordsPerPage);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<?php include 'inc/header.php'; ?>

<main class="p-4 mt-40">

    <div class="container mx-auto p-4">

        <div class="bg-white rounded-lg shadow-lg p-6">

            <h1 class="text-2xl text-center font-semibold mb-4">My Orders</h1>

            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">Payment Method</th>
                        <th class="border px-4 py-2">Order Date</th>
                        <th class="border px-4 py-2">Order Status</th> <!-- New column for Order Status -->
                        <th class="border px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['order_status']); ?></td> <!-- Display Order Status -->
                            <td class="border px-4  text-center py-4">
                                <a href="view_order.php?order_id=<?php echo $row['id']; ?>" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">View</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="flex justify-between mt-4">
                <a href="?page=<?php echo max(1, $page - 1); ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Prev</a>
                <div>Page <?php echo $page; ?> of <?php echo $totalPages; ?></div>
                <a href="?page=<?php echo min($totalPages, $page + 1); ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Next</a>
            </div>

        </div>

    </div>

<?php
// Check if the order confirmation message should be displayed
if (isset($_GET['orderConfirmed']) && $_GET['orderConfirmed'] === 'true') {
    // JavaScript to display a styled message for 2 seconds
    echo '
    <script>
        // Create a div element for the message
        var messageDiv = document.createElement("div");
        
        // Set the message and styling
        messageDiv.textContent = "Order confirmed successfully!";
        messageDiv.style.backgroundColor = "#34D399"; // Green 500
        messageDiv.style.color = "#fff";
        messageDiv.style.padding = "10px";
        messageDiv.style.borderRadius = "5px";
        messageDiv.style.position = "fixed";
        messageDiv.style.top = "50%";
        messageDiv.style.left = "50%";
        messageDiv.style.transform = "translate(-50%, -50%)";
        messageDiv.style.zIndex = "9999";
        messageDiv.style.textAlign = "center"; // Center text
        
        // Append the message to the body
        document.body.appendChild(messageDiv);
        
        // Remove the message after 2 seconds
        setTimeout(function() {
            messageDiv.remove();
        }, 2000);
    </script>';
}
?>


</main>

<?php include 'inc/footer.php'; ?>

</body>
</html>
