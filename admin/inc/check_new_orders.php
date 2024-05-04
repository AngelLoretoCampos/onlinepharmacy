<?php
// Database connection
include 'dbconnection.php';

// Check for new orders
$sql = "SELECT MAX(id) AS latest_order_id FROM orders";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $latestOrderId = $row['latest_order_id'];
    echo $latestOrderId;
} else {
    echo "0";
}

// Close database connection
$conn->close();
?>
