<?php
include '../database/dbconnection.php';

// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user's ID from URL parameter
if(isset($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    // Handle case where user ID is not provided
    echo "User ID is not provided.";
    exit;
}

// Fetch user's information
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bindParam(1, $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user's orders, ordered by most recent first
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bindParam(1, $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include sidebar
include 'inc/sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 ml-64">

<div class="container px-6 mx-auto bg-white mt-20 py-6">
    <h1 class="text-2xl font-bold mb-6">Order History for <?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h1>

    <table class="min-w-full bg-white shadow-md rounded">
        <!-- Table headers -->
        <thead>
            <tr class="bg-gray-800 text-white">
                <th class="px-4 py-2">Order ID</th>
                <th class="px-4 py-2">Order Date</th>
                <th class="px-4 py-2">Total Amount</th>
                <th class="px-4 py-2">Order Status</th>
                <th class="px-4 py-2">View</th>
            </tr>
        </thead>
        <!-- Table body -->
        <tbody>
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo $order['id']; ?></td>
                        <td class="border px-4 py-2"><?php echo $order['order_date']; ?></td>
                        <td class="border px-4 py-2">₱<?php echo $order['total_amount']; ?></td>
                        <td class="border px-4 py-2"><?php echo $order['order_status']; ?></td>
                        <td class="border px-4 py-2">
                            <a href="view_order.php?order_id=<?php echo $order['id']; ?>" class="text-blue-500 hover:underline">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="border px-4 py-2 text-center">No records to be displayed.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
