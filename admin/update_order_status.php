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

if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $order_status, $order_id);

    if ($stmt->execute()) {
        // If the order is cancelled, restore product quantities
        if ($order_status === 'Cancelled') {
            $stmt_items = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();

            while ($row = $result_items->fetch_assoc()) {
                $product_id = $row['product_id'];
                $quantity = $row['quantity'];

                // Update product quantity
                $stmt_update = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                $stmt_update->bind_param("ii", $quantity, $product_id);
                $stmt_update->execute();
            }

            $stmt_update->close();
            $stmt_items->close();
        }

        // Redirect back to view_order.php with success message
        header("Location: view_order.php?order_id=$order_id&success=Order status updated successfully!");
        exit();
    } else {
        // Redirect back to view_order.php with error message
        header("Location: view_order.php?order_id=$order_id&error=Error updating order status: " . $stmt->error);
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect back to view_order.php with invalid request message
    header("Location: view_order.php?error=Invalid request!");
    exit();
}
?>
