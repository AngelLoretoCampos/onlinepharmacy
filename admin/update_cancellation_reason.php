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
    $order_id = $_POST['order_id'];
    $cancellation_reason = $_POST['cancellation_reason'];

    // Update cancellation reason in the database
    $stmt = $conn->prepare("UPDATE orders SET cancellation_reason = ? WHERE id = ?");
    $stmt->bind_param("si", $cancellation_reason, $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // JavaScript alert message
        echo "<script>alert('Cancellation reason saved successfully! Click OK to continue.');</script>";
        // Redirect to view_order.php after 1 second
        echo "<script>setTimeout(function() { window.location.href = 'view_order.php?order_id=$order_id'; }, 1000);</script>";
    } else {
        // JavaScript alert message
        echo "<script>alert('Failed to save cancellation reason!');</script>";
        // Redirect to view_order.php after 1 second
        echo "<script>setTimeout(function() { window.location.href = 'view_order.php?order_id=$order_id'; }, 1000);</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
