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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    // Get order_id and order_status from POST data
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // Prepare and execute SQL statement to update order status
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $order_status, $order_id);
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
        $message = "Order status updated successfully.";

        // If the order is being canceled, restore product quantities
        if ($order_status === "Cancelled") {
            // Retrieve order items
            $order_items_query = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
            $order_items_stmt = $conn->prepare($order_items_query);
            $order_items_stmt->bind_param("i", $order_id);
            $order_items_stmt->execute();
            $order_items_result = $order_items_stmt->get_result();

            // Restore product quantities
            while ($row = $order_items_result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $quantity = $row['quantity'];
                
                // Increment product quantity
                $update_product_query = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
                $update_product_stmt = $conn->prepare($update_product_query);
                $update_product_stmt->bind_param("ii", $quantity, $product_id);
                $update_product_stmt->execute();
            }

            // Close order items statement
            $order_items_stmt->close();
        }
    } else {
        $message = "Failed to update order status.";
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();

    // Set confirmation message
    $_SESSION['confirmation_message'] = $message;

    // Redirect back to the order details page after 1 second
    header("refresh:1;url=view_order.php?order_id=$order_id");
    exit;
} else {
    // If the request method is not POST, redirect back to the view order page
    header("Location: view_order.php?order_id=$order_id");
    exit;
}
?>
